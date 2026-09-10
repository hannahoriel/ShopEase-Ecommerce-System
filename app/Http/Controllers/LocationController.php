<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Serves the Philippine province / city-municipality / barangay
 * cascading dropdown data.
 *
 * This is the Laravel port of the original Flask app, which simply
 * rendered a template and let the browser call the public PSGC /
 * Buonzz APIs directly. Here the same "try PSGC, fall back to
 * Buonzz" logic now lives on the server, behind our own JSON
 * endpoints, and results are cached since PH location data changes
 * essentially never.
 */
class LocationController extends Controller
{
    /**
     * Render the location picker page.
     */
    public function index()
    {
        return view('location');
    }

    /**
     * GET /api/locations/provinces
     *
     * Returns all provinces, plus a synthetic "Metro Manila (NCR)"
     * entry (NCR isn't a province, so the upstream APIs don't
     * return it here) so the UI can offer it the same way the
     * original app did.
     */
    public function provinces(): JsonResponse
    {
        $provinces = Cache::remember('locations:provinces', config('locations.cache_ttl'), function () {
            $data = $this->fetchWithFallback('provinces');

            $hasNcr = collect($data)->contains(
                fn ($p) => str_contains($p['name'] ?? '', 'National Capital Region')
            );

            if (! $hasNcr) {
                $data[] = [
                    'code'     => config('locations.ncr_code'),
                    'name'     => 'Metro Manila (NCR)',
                    'isRegion' => true,
                ];
            }

            return collect($data)->sortBy('name')->values()->all();
        });

        return response()->json($provinces);
    }

    /**
     * GET /api/locations/provinces/{code}/cities?is_region=1
     *
     * Cities/municipalities for a given province code. Pass
     * ?is_region=1 for the synthetic NCR "province" to fetch its
     * cities from the regions endpoint instead.
     */
    public function municipalities(Request $request, string $code): JsonResponse
    {
        $isRegion = $request->boolean('is_region') || $code === config('locations.ncr_code');

        $cacheKey = "locations:municipalities:{$code}:" . ($isRegion ? 'region' : 'province');

        $municipalities = Cache::remember($cacheKey, config('locations.cache_ttl'), function () use ($code, $isRegion) {
            if ($isRegion) {
                $url = config('locations.base_urls.psgc') . "/regions/{$code}/cities-municipalities/";
                $data = $this->getJson($url) ?? [];
            } else {
                $data = $this->fetchWithFallback('municipalities', $code);
            }

            return collect($data)->sortBy('name')->values()->all();
        });

        return response()->json($municipalities);
    }

    /**
     * GET /api/locations/cities/{code}/barangays
     */
    public function barangays(string $code): JsonResponse
    {
        $barangays = Cache::remember("locations:barangays:{$code}", config('locations.cache_ttl'), function () use ($code) {
            $data = $this->fetchWithFallback('barangays', $code);

            return collect($data)->sortBy(fn ($b) => $b['name'] ?? $b['brgy_name'] ?? '')->values()->all();
        });

        return response()->json($barangays);
    }

    /**
     * Try each configured upstream source in order until one
     * succeeds, mirroring the original front-end's fetchWithFallbacks().
     */
    private function fetchWithFallback(string $endpointType, ?string $code = null): array
    {
        foreach (config('locations.fallback_order') as $source) {
            $url = $this->buildUrl($source, $endpointType, $code);

            if (! $url) {
                continue;
            }

            $data = $this->getJson($url);

            if (is_array($data)) {
                return $data;
            }

            Log::info("Location lookup failed via {$source}, trying next source.", [
                'endpoint' => $endpointType,
                'code'     => $code,
            ]);
        }

        throw new \RuntimeException("All upstream location APIs failed for [{$endpointType}].");
    }

    private function buildUrl(string $source, string $endpointType, ?string $code): ?string
    {
        $base = config("locations.base_urls.{$source}");

        return match ([$source, $endpointType]) {
            ['psgc', 'provinces'] => "{$base}/provinces/",
            ['buonzz', 'provinces'] => "{$base}/provinces",
            ['psgc', 'municipalities'] => "{$base}/provinces/{$code}/cities-municipalities/",
            ['buonzz', 'municipalities'] => "{$base}/provinces/{$code}/cities",
            ['psgc', 'barangays'] => "{$base}/cities-municipalities/{$code}/barangays/",
            ['buonzz', 'barangays'] => "{$base}/cities/{$code}/barangays",
            default => null,
        };
    }

    private function getJson(string $url): ?array
    {
        try {
            $response = Http::timeout(config('locations.timeout'))
                ->withOptions(['verify' => config('locations.verify_ssl')])
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            $json = $response->json();

            return is_array($json) ? $json : null;
        } catch (\Throwable $e) {
            Log::warning("Failed to fetch location data from {$url}: {$e->getMessage()}");

            return null;
        }
    }
}
