<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller\Product;
use App\Models\Seller\ProductReview;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerFeedbackController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $seller = $this->sellerFor($request->user());
        $request->validate([
            'rating' => ['nullable', 'integer', 'between:1,5'],
        ]);
        $query = ProductReview::query()
            ->where('seller_id', $seller->id)
            ->with(['product:id,name,photos', 'buyer:id,name'])
            ->latest();

        if ($request->filled('rating') && $request->input('rating') !== 'all') {
            $query->where('rating', $request->integer('rating'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($builder) use ($search): void {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%")
                    ->orWhereHas('buyer', fn ($buyer) => $buyer->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('product', fn ($product) => $product->where('name', 'like', "%{$search}%"));
            });
        }

        $reviews = $query->paginate(min(max($request->integer('per_page', 7), 1), 50));

        $products = Product::query()
            ->where('seller_id', $seller->id)
            ->whereHas('reviews')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product): array => [
                'id' => $product->id,
                'name' => $product->name,
                'image_url' => $this->productImage($product),
                'average_rating' => round((float) $product->reviews_avg_rating, 1),
                'total_reviews' => (int) $product->reviews_count,
            ])
            ->values();

        return response()->json([
            'feedback' => [
                'data' => $reviews->getCollection()->map(fn (ProductReview $review): array => $this->serializeReview($review))->values(),
                'total' => $reviews->total(),
                'per_page' => $reviews->perPage(),
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
            ],
            'products' => $products,
        ]);
    }

    public function showProduct(Request $request, Product $product): JsonResponse
    {
        $seller = $this->sellerFor($request->user());
        abort_unless($product->seller_id === $seller->id, 404);

        $reviews = ProductReview::query()
            ->where('product_id', $product->id)
            ->with('buyer:id,name')
            ->latest()
            ->paginate(10);
        $averageRating = (float) ProductReview::where('product_id', $product->id)->avg('rating');
        $counts = ProductReview::query()
            ->where('product_id', $product->id)
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating')
            ->map(fn ($count): int => (int) $count)
            ->all();

        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'image_url' => $this->productImage($product),
            ],
            'summary' => [
                'average_rating' => round($averageRating, 1),
                'total_reviews' => $reviews->total(),
                'rating_counts' => array_replace(array_fill(1, 5, 0), $counts),
            ],
            'data' => $reviews->getCollection()->map(fn (ProductReview $review): array => $this->serializeReview($review))->values(),
            'total' => $reviews->total(),
        ]);
    }

    private function sellerFor(User $user): Seller
    {
        return Seller::where('user_id', $user->id)->firstOrFail();
    }

    private function serializeReview(ProductReview $review): array
    {
        return [
            'id' => $review->id,
            'order_id' => null,
            'customer' => $review->buyer?->name ?? 'ShopEase customer',
            'phone' => $review->buyer?->buyerProfile?->contact_no ?? '',
            'rating' => $review->rating,
            'title' => $review->title ?: 'Customer feedback',
            'body' => $review->body,
            'product' => $review->product?->name,
            'product_id' => $review->product_id,
            'created_at' => $review->created_at?->toISOString(),
        ];
    }

    private function productImage(Product $product): ?string
    {
        $photo = collect($product->photos ?? [])->first(fn ($value) => is_string($value) && trim($value) !== '');
        if (! $photo) {
            return null;
        }

        if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://') || str_starts_with($photo, 'data:')) {
            return $photo;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url(ltrim(preg_replace('/^storage\//', '', $photo), '/'));
    }
}
