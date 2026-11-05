<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ShopController extends Controller
{
    public function following(Request $request): JsonResponse
    {
        $rows = DB::table('buyer_shop_follows as follows')
            ->join('sellers', 'sellers.id', '=', 'follows.seller_id')
            ->leftJoin('users', 'users.id', '=', 'sellers.user_id')
            ->where('follows.buyer_id', $request->user()->id)
            ->where('sellers.registration_status', 'active')
            ->orderByDesc('follows.created_at')
            ->select(['sellers.id', 'sellers.store_name', 'sellers.business_name', 'sellers.province', 'sellers.municipality', 'sellers.user_id', 'users.name as owner_name', 'users.profile_photo_path', 'follows.created_at as followed_at'])
            ->get()
            ->map(function ($shop): array {
                return [
                    'id' => (int) $shop->id,
                    'name' => $shop->store_name ?: $shop->business_name ?: $shop->owner_name,
                    'owner_name' => $shop->owner_name,
                    'avatar_url' => $shop->profile_photo_path ? Storage::disk('public')->url($shop->profile_photo_path) : null,
                    'location' => collect([$shop->municipality, $shop->province])->filter()->implode(', '),
                    'product_count' => DB::table('products')->where('seller_id', $shop->id)->where('status', 'active')->where('is_archived', false)->count(),
                    'rating_average' => round((float) DB::table('product_reviews')->where('seller_id', $shop->id)->avg('rating'), 1),
                    'rating_count' => DB::table('product_reviews')->where('seller_id', $shop->id)->count(),
                    'followers' => DB::table('buyer_shop_follows')->where('seller_id', $shop->id)->count(),
                    'followed_at' => $shop->followed_at,
                ];
            })->values();

        return response()->json(['data' => $rows, 'total' => $rows->count()]);
    }

    public function show(Request $request, Seller $seller): JsonResponse
    {
        abort_unless($seller->registration_status === 'active', 404);

        $products = $seller->products()
            ->where('status', 'active')
            ->where('is_archived', false)
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->withSum(['orderItems as sold_count' => fn ($items) => $items->whereHas(
                'order', fn ($orders) => $orders->whereIn('status', ['delivered', 'completed'])
            )], 'quantity');

        $shopCategories = $seller->products()
            ->where('status', 'active')
            ->where('is_archived', false)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            $products->where(fn ($query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%"));
        }
        if ($request->filled('category')) {
            $categoryKey = $this->categoryKey((string) $request->query('category'));
            $categoryValues = $shopCategories
                ->filter(fn ($category): bool => $this->categoryKey((string) $category) === $categoryKey)
                ->values();
            $products->whereIn('category', $categoryValues);
        }

        match ($request->query('sort')) {
            'popular' => $products->orderByDesc('sold_count')->orderByDesc('reviews_avg_rating')->latest(),
            'latest' => $products->latest(),
            'top_sales' => $products->orderByDesc('sold_count')->latest(),
            'price_low' => $products->orderBy('price')->latest(),
            'price_high' => $products->orderByDesc('price')->latest(),
            default => $products->latest(),
        };

        $page = $products->paginate(min(max((int) $request->query('per_page', 24), 1), 60));
        $seller->load(['user:id,name,profile_photo_path', 'reviews']);
        $allProductCount = $seller->products()->where('status', 'active')->where('is_archived', false)->count();

        // Show actual active products from other sellers after this shop's collection.
        // This is independent of the current shop's search/category filters.
        $recommendedProducts = Product::query()
            ->where('seller_id', '!=', $seller->id)
            ->where('status', 'active')
            ->where('is_archived', false)
            ->whereHas('seller', fn ($query) => $query->where('registration_status', 'active'))
            ->with('seller:id,store_name,business_name')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->inRandomOrder()
            ->limit(10)
            ->get();

        return response()->json([
            'shop' => [
                'id' => $seller->id,
                'name' => $seller->store_name ?: $seller->business_name ?: $seller->user?->name,
                'owner_name' => $seller->user?->name,
                'avatar_url' => $seller->profile_photo_path ? Storage::disk('public')->url($seller->profile_photo_path) : ($seller->user?->profile_photo_path ? Storage::disk('public')->url($seller->user->profile_photo_path) : null),
                'cover_url' => $seller->cover_photo_path ? Storage::disk('public')->url($seller->cover_photo_path) : null,
                'compliance_score' => null, // UI placeholder until authoritative admin compliance score integration
                'location' => collect([$seller->municipality, $seller->province])->filter()->implode(', '),
                'joined_at' => ($seller->approved_at ?? $seller->created_at)?->toISOString(),
                'product_count' => $allProductCount,
                'rating_average' => round((float) $seller->reviews()->avg('rating'), 1),
                'rating_count' => $seller->reviews()->count(),
                'followers' => DB::table('buyer_shop_follows')->where('seller_id', $seller->id)->count(),
                'is_following' => DB::table('buyer_shop_follows')->where('seller_id', $seller->id)->where('buyer_id', $request->user()->id)->exists(),
                'categories' => $shopCategories
                    ->filter(fn ($category): bool => trim((string) $category) !== '')
                    ->groupBy(fn ($category): string => $this->categoryKey((string) $category))
                    ->map(fn ($categories, $key): array => [
                        'value' => $key,
                        'label' => $this->categoryLabel((string) $categories->first()),
                    ])
                    ->sortBy('label', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values(),
            ],
            'data' => $page->getCollection()->map(fn (Product $product): array => $this->serializeProduct($product))->values(),
            'recommendations' => $recommendedProducts->map(fn (Product $product): array => array_merge($this->serializeProduct($product), [
                'shop_name' => $product->seller?->store_name ?: $product->seller?->business_name ?: 'ShopEase Seller',
            ]))->values(),
            'total' => $page->total(),
            'current_page' => $page->currentPage(),
            'last_page' => $page->lastPage(),
        ]);
    }

    public function follow(Request $request, Seller $seller): JsonResponse
    {
        abort_unless($seller->registration_status === 'active', 404);
        DB::table('buyer_shop_follows')->insertOrIgnore([
            'buyer_id' => $request->user()->id,
            'seller_id' => $seller->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this->followState($request, $seller, true);
    }

    public function unfollow(Request $request, Seller $seller): JsonResponse
    {
        DB::table('buyer_shop_follows')->where('buyer_id', $request->user()->id)->where('seller_id', $seller->id)->delete();

        return $this->followState($request, $seller, false);
    }

    private function followState(Request $request, Seller $seller, bool $following): JsonResponse
    {
        return response()->json([
            'is_following' => $following,
            'followers' => DB::table('buyer_shop_follows')->where('seller_id', $seller->id)->count(),
        ]);
    }

    private function serializeProduct(Product $product): array
    {
        $photos = collect($product->photos ?? [])->map(function ($photo): ?string {
            if (! is_string($photo) || trim($photo) === '') return null;
            if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://') || str_starts_with($photo, 'data:')) return $photo;
            return Storage::disk('public')->url(ltrim(preg_replace('/^storage\//', '', $photo), '/'));
        })->filter()->values();

        return [
            'id' => $product->id,
            'name' => $product->name,
            'category' => $product->category,
            'price' => (float) $product->price,
            'sale_percentage' => (int) $product->sale_percentage,
            'sale_price' => $product->sale_price,
            'stock' => (int) $product->stock_quantity,
            'sold_count' => (int) ($product->sold_count ?? 0),
            'rating' => round((float) ($product->reviews_avg_rating ?? 0), 1),
            'rating_count' => (int) ($product->reviews_count ?? 0),
            'photos' => $photos,
            'image_url' => $photos->first(),
        ];
    }

    private function categoryKey(string $category): string
    {
        return (string) preg_replace('/[^a-z0-9]+/', '', strtolower(trim($category)));
    }

    private function categoryLabel(string $category): string
    {
        if ($this->categoryKey($category) === 'womensapparel') {
            return "Women's Apparel";
        }

        $words = preg_replace('/[_-]+/', ' ', trim($category));

        return ucwords((string) $words);
    }
}
