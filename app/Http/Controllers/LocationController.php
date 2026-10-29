<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    /**
     * Render the location picker page.
     */
    public function index()
    {
        return view('location');
    }

    public function regions(): JsonResponse
    {
        return response()->json($this->fetchPsgcCached('regions', '/regions'));
    }

    public function regionProvinces(string $code): JsonResponse
    {
        return response()->json(
            $this->fetchPsgcCached("regions:{$code}:provinces", "/regions/{$code}/provinces")
        );
    }

    public function regionDistricts(string $code): JsonResponse
    {
        return response()->json(
            $this->fetchPsgcCached("regions:{$code}:districts", "/regions/{$code}/districts")
        );
    }

    public function regionCities(string $code): JsonResponse
    {
        return response()->json(
            $this->fetchPsgcCached(
                "regions:{$code}:cities",
                "/v2/regions/{$code}/cities-municipalities"
            )
        );
    }

    public function provinceCities(string $code): JsonResponse
    {
        return response()->json(
            $this->fetchPsgcCached(
                "provinces:{$code}:cities",
                "/provinces/{$code}/cities-municipalities"
            )
        );
    }

    public function districtCities(string $code): JsonResponse
    {
        return response()->json(
            $this->fetchPsgcCached(
                "districts:{$code}:cities",
                "/districts/{$code}/cities-municipalities"
            )
        );
    }

    public function cityBarangays(string $code): JsonResponse
    {
        return response()->json(
            $this->fetchPsgcCached(
                "cities:{$code}:barangays",
                "/cities-municipalities/{$code}/barangays"
            )
        );
    }

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


    public function municipalities(Request $request, string $code): JsonResponse
    {
        $isRegion = $request->boolean('is_region') || $code === config('locations.ncr_code');

        $cacheKey = "locations:municipalities:{$code}:" . ($isRegion ? 'region' : 'province');

        $municipalities = Cache::remember($cacheKey, config('locations.cache_ttl'), function () use ($code, $isRegion) {
            if ($isRegion) {
                $data = $this->fetchPsgcCached(
                    "regions:{$code}:cities",
                    "/regions/{$code}/cities-municipalities"
                );
            } else {
                $data = $this->fetchWithFallback('municipalities', $code);
            }

            return collect($data)->sortBy('name')->values()->all();
        });

        return response()->json($municipalities);
    }


    public function barangays(string $code): JsonResponse
    {
        $barangays = Cache::remember("locations:barangays:{$code}", config('locations.cache_ttl'), function () use ($code) {
            $data = $this->fetchWithFallback('barangays', $code);

            return collect($data)->sortBy(fn ($b) => $b['name'] ?? $b['brgy_name'] ?? '')->values()->all();
        });

        return response()->json($barangays);
    }


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

    private function fetchPsgcCached(string $key, string $path): array
    {
        return Cache::remember("locations:psgc:{$key}", config('locations.cache_ttl'), function () use ($path): array {
            $url = rtrim(config('locations.base_urls.psgc'), '/').$path;
            $data = $this->getJson($url);
            if ($data === null) {
                throw new \RuntimeException("The PSGC location service failed for [{$path}].");
            }

            return collect($data)->sortBy('name')->values()->all();
        });
    }
}
