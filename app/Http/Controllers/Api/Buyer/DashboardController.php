<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Seller\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function products(Request $request): JsonResponse
    {
        $query = Product::with(['seller', 'options', 'productSpecifications'])
            ->where('is_archived', false)
            ->where('status', 'active')
            ->whereHas('seller', fn ($q) => $q->where('registration_status', 'active'));

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = min((int) $request->input('per_page', 24), 100);
        $products = $query->latest()->paginate($perPage);

        return response()->json([
            'data'         => $products->map(fn (Product $p) => $this->serializeProduct($p))->values(),
            'total'        => $products->total(),
            'per_page'     => $products->perPage(),
            'current_page' => $products->currentPage(),
            'last_page'    => $products->lastPage(),
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        abort_unless(
            !$product->is_archived && $product->status === 'active',
            404
        );

        $product->load(['seller', 'options', 'productSpecifications']);

        return response()->json($this->serializeProduct($product, true));
    }

    private function serializeProduct(Product $product, bool $full = false): array
    {
        $photos = $this->resolvePhotos($product->photos ?? []);

        $data = [
            'id'            => $product->id,
            'name'          => $product->name,
            'price'         => (float) $product->price,
            'stock'         => $product->stock_quantity,
            'category'      => $product->category,
            'photos'        => $photos,
            'image_url'     => $photos[0] ?? null,
            'pricing_mode'  => $product->pricing_mode,
            'seller' => [
                'id'            => $product->seller->id,
                'store_name'    => $product->seller->store_name,
                'joined_years'  => $product->seller->approved_at
                    ? (int) $product->seller->approved_at->diffInYears(now())
                    : 0,
                'joined_months' => $product->seller->approved_at
                    ? (int) $product->seller->approved_at->diffInMonths(now())
                    : 0,
                'product_count' => $product->seller->products()
                    ->where('status', 'active')
                    ->where('is_archived', false)
                    ->count(),
                'ratings'       => 0,
                'followers'     => 0,
                'response_rate' => '100%',
                'response_time' => 'within minutes',
            ],
        ];

        if ($full) {
            $data['description']      = $product->description;
            $data['sku']              = $product->sku;
            $data['pricing_source']   = $product->pricing_source;
            $data['variations']       = $product->variations;
            $data['colors']           = $product->colors;
            $data['sizes']            = $product->sizes;
            $data['specifications']   = $product->specifications;
        }

        return $data;
    }

    private function resolvePhotos(array $photos): array
    {
        return array_values(array_filter(array_map(function ($photo): ?string {
            if (!is_string($photo) || trim($photo) === '') {
                return null;
            }

            $value = trim($photo);

            if (
                str_starts_with($value, 'data:') ||
                str_starts_with($value, 'http://') ||
                str_starts_with($value, 'https://')
            ) {
                return $value;
            }

            $path = ltrim(preg_replace('/^storage\//', '', $value), '/');

            return $path !== '' ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : null;
        }, $photos)));
    }
}
