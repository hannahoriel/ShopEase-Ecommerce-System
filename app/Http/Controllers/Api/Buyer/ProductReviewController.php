<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Seller\Product;
use App\Models\Seller\ProductReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductReviewController extends Controller
{
    public function index(Request $request, Product $product): JsonResponse
    {
        abort_unless($product->status === 'active' && ! $product->is_archived, 404);

        $validated = $request->validate([
            'rating' => ['nullable', 'integer', 'between:1,5'],
        ]);

        $reviews = ProductReview::query()
            ->where('product_id', $product->id)
            ->when(isset($validated['rating']), fn ($query) => $query->where('rating', $validated['rating']))
            ->with('buyer:id,name,profile_photo_path')
            ->latest()
            ->paginate(10);

        return response()->json([
            'summary' => $this->summary($product->id),
            'data' => $reviews->getCollection()->map(fn (ProductReview $review): array => $this->serializeReview($review))->values(),
            'total' => $reviews->total(),
            'per_page' => $reviews->perPage(),
            'current_page' => $reviews->currentPage(),
            'last_page' => $reviews->lastPage(),
        ]);
    }

    public function store(Request $request, Product $product): JsonResponse
    {
        abort_unless(
            $product->status === 'active' && ! $product->is_archived,
            404
        );

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:120'],
            'body' => ['required', 'string', 'min:2', 'max:2000'],
        ]);

        $buyerId = $request->user()->id;
        $hasCompletedPurchase = Order::query()
            ->where('buyer_id', $buyerId)
            ->where('status', 'completed')
            ->whereHas('items', fn ($query) => $query->where('product_id', $product->id))
            ->exists();
        if (! $hasCompletedPurchase) {
            throw ValidationException::withMessages([
                'review' => ['Confirm receipt of a completed purchase before rating this seller.'],
            ]);
        }

        if (ProductReview::where('product_id', $product->id)->where('buyer_id', $buyerId)->exists()) {
            throw ValidationException::withMessages([
                'review' => ['You have already reviewed this product.'],
            ]);
        }

        $review = ProductReview::create([
            ...$validated,
            'product_id' => $product->id,
            'seller_id' => $product->seller_id,
            'buyer_id' => $buyerId,
        ]);
        $review->load('buyer:id,name,profile_photo_path');

        return response()->json([
            'message' => 'Your review has been submitted.',
            'review' => $this->serializeReview($review),
            'summary' => $this->summary($product->id),
        ], 201);
    }

    private function summary(int $productId): array
    {
        $reviews = ProductReview::query()
            ->where('product_id', $productId)
            ->selectRaw('COUNT(*) as total, AVG(rating) as average')
            ->first();

        $counts = ProductReview::query()
            ->where('product_id', $productId)
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating')
            ->map(fn ($count): int => (int) $count)
            ->all();

        return [
            'average_rating' => round((float) $reviews->average, 1),
            'total_reviews' => (int) $reviews->total,
            'rating_counts' => array_replace(array_fill(1, 5, 0), $counts),
        ];
    }

    private function serializeReview(ProductReview $review): array
    {
        return [
            'id' => $review->id,
            'rating' => $review->rating,
            'title' => $review->title,
            'body' => $review->body,
            'buyer_name' => $review->buyer?->name ?? 'ShopEase customer',
            'buyer_photo_url' => $review->buyer?->profile_photo_path
                ? Storage::disk('public')->url($review->buyer->profile_photo_path)
                : null,
            'created_at' => $review->created_at?->toISOString(),
        ];
    }
}
