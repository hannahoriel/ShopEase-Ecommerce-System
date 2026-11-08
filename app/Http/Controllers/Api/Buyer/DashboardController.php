<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Admin\Announcement;
use App\Models\Seller\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function announcements(): JsonResponse
    {
        Announcement::query()
            ->where('status', 'Scheduled')
            ->where('published_at', '<=', now())
            ->update(['status' => 'Published', 'is_active' => true]);

        $announcements = Announcement::publishedForAudience([
            'All Users',
            'Buyers',
        ])
            ->get()
            ->map(fn (Announcement $announcement) => [
                'id' => $announcement->id,
                'type' => $announcement->type ?? 'Announcement',
                'title' => $announcement->title,
                'description' => $announcement->body,
                'banner_url' => $announcement->banner_path
                    ? Storage::disk('public')->url($announcement->banner_path)
                    : null,
            ])
            ->values();

        return response()->json(['data' => $announcements]);
    }

    public function products(Request $request): JsonResponse
    {
        $query = Product::with(['seller' => fn ($seller) => $seller->withAvg('reviews', 'rating')->withCount('reviews'), 'options', 'productSpecifications'])
            ->withAvg('reviews as product_rating_average', 'rating')
            ->withCount('reviews as product_rating_count')
            ->withSum(['orderItems as sold_count' => fn ($items) => $items->whereHas(
                'order', fn ($orders) => $orders->whereIn('status', ['delivered', 'completed'])
            )], 'quantity')
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
        // Sort the entire matching catalog before pagination, using the discounted price.
        // Default remains latest to preserve the Buyer Dashboard behavior.
        $sort = (string) $request->query('sort', 'latest');
        if (in_array($sort, ['price-low', 'price-high'], true)) {
            $direction = $sort === 'price-low' ? 'asc' : 'desc';
            $query->orderByRaw(
                'ROUND(COALESCE(price, 0) * (100 - COALESCE(sale_percentage, 0)) / 100, 2) ' . $direction
            )->orderBy('id', 'desc');
        } else {
            $query->latest();
        }
        $products = $query->paginate($perPage);

        return response()->json([
            'data' => $products->map(fn (Product $p) => $this->serializeProduct($p))->values(),
            'total' => $products->total(),
            'per_page' => $products->perPage(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        abort_unless(
            ! $product->is_archived && $product->status === 'active',
            404
        );

        $product->loadAvg('reviews as product_rating_average', 'rating');
        $product->loadCount('reviews as product_rating_count');
        $product->load(['seller' => fn ($seller) => $seller->withAvg('reviews', 'rating')->withCount('reviews'), 'options', 'productSpecifications', 'connectedVariants']);
        $product->loadSum(['orderItems as sold_count' => fn ($items) => $items->whereHas(
            'order', fn ($orders) => $orders->whereIn('status', ['delivered', 'completed'])
        )], 'quantity');

        return response()->json($this->serializeProduct($product, true));
    }

    private function serializeProduct(Product $product, bool $full = false): array
    {
        $photos = $this->resolvePhotos($product->photos ?? []);

        $data = [
            'id' => $product->id,
            'name' => $product->name,
            'is_fragile' => (bool) $product->is_fragile,
            'price' => (float) $product->price,
            'original_price' => (float) $product->price,
            'sale_percentage' => $product->sale_percentage,
            'sale_price' => $product->sale_price,
            'stock' => $product->stock_quantity,
            'sold_count' => (int) ($product->sold_count ?? 0),
            'rating_average' => round((float) ($product->product_rating_average ?? 0), 1),
            'rating_count' => (int) ($product->product_rating_count ?? 0),
            'category' => $product->category,
            'photos' => $photos,
            'image_url' => $photos[0] ?? null,
            'pricing_mode' => $product->pricing_mode,
            'seller' => [
                'id' => $product->seller->id,
                'store_name' => $product->seller->store_name,
                'joined_years' => $product->seller->approved_at
                    ? (int) $product->seller->approved_at->diffInYears(now())
                    : 0,
                'joined_months' => $product->seller->approved_at
                    ? (int) $product->seller->approved_at->diffInMonths(now())
                    : 0,
                'product_count' => $product->seller->products()
                    ->where('status', 'active')
                    ->where('is_archived', false)
                    ->count(),
                'ratings' => round((float) ($product->seller->reviews_avg_rating ?? 0), 1),
                'rating_count' => (int) ($product->seller->reviews_count ?? 0),
                'followers' => 0,
                'response_rate' => '100%',
                'response_time' => 'within minutes',
            ],
        ];

        if ($full) {
            $data['description'] = $product->description;
            $data['sku'] = $product->sku;
            $data['pricing_source'] = $product->pricing_source;
            $data['variations'] = $product->variations;
            $data['colors'] = $product->colors;
            $data['sizes'] = $product->sizes;
            $data['connected_variants'] = $product->connected_variants;
            $data['specifications'] = $product->specifications;
        }

        return $data;
    }

    private function resolvePhotos(array $photos): array
    {
        return array_values(array_filter(array_map(function ($photo): ?string {
            if (! is_string($photo) || trim($photo) === '') {
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

            return $path !== '' ? Storage::disk('public')->url($path) : null;
        }, $photos)));
    }
}
