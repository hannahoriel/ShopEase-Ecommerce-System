<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Seller\Product;
use App\Models\Seller\ProductReview;
use App\Services\SellerNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductReviewController extends Controller
{
    public function index(Request $request, Product $product): JsonResponse
    {
        abort_unless($product->status === 'active' && ! $product->is_archived, 404);

        $validated = $request->validate([
            'rating' => ['nullable', 'integer', 'between:1,5'],
        ]);

        $query = ProductReview::query()
            ->where('product_id', $product->id)
            ->when(isset($validated['rating']), fn ($query) => $query->where('rating', $validated['rating']))
            ->with('buyer:id,name,profile_photo_path')
            ->latest();

        $reviews = $request->boolean('all')
            ? $query->get()
            : $query->limit(3)->get();

        return response()->json([
            'summary' => $this->summary($product->id),
            'data' => $reviews->map(fn (ProductReview $review): array => $this->serializeReview($review))->values(),
            'total' => ProductReview::where('product_id', $product->id)->when(isset($validated['rating']), fn ($query) => $query->where('rating', $validated['rating']))->count(),
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
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_anonymous' => ['sometimes', 'boolean'],
        ]);

        $buyerId = $request->user()->id;
        $hasCompletedPurchase = Order::query()
            ->where('buyer_id', $buyerId)
            ->where('status', 'completed')
            ->whereHas('items', fn ($query) => $query->where('product_id', $product->id))
            ->exists();
        if (! $hasCompletedPurchase) {
            throw ValidationException::withMessages([
                'review' => ['Confirm receipt of a completed purchase before rating this product.'],
            ]);
        }

        if (ProductReview::where('product_id', $product->id)->where('buyer_id', $buyerId)->exists()) {
            throw ValidationException::withMessages([
                'review' => ['You have already reviewed this product.'],
            ]);
        }

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('product-reviews', 'public')
            : null;
        unset($validated['photo']);

        $review = ProductReview::create([
            ...$validated,
            'photo_path' => $photoPath,
            'is_anonymous' => (bool) ($validated['is_anonymous'] ?? false),
            'product_id' => $product->id,
            'seller_id' => $product->seller_id,
            'buyer_id' => $buyerId,
        ]);
        $review->load('buyer:id,name,profile_photo_path');

        $sellerUser = $product->seller?->user;
        if ($sellerUser) {
            app(SellerNotificationService::class)->send(
                $sellerUser,
                'reviews',
                'New product review',
                'A buyer left a '.(int) $review->rating.'-star review for '.$product->name.'.',
                '/seller/performance',
            );
        }

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
        $buyerName = $review->buyer?->name ?? 'ShopEase customer';

        return [
            'id' => $review->id,
            'rating' => $review->rating,
            'title' => $review->title,
            'body' => $review->body,
            'buyer_name' => $review->is_anonymous ? $this->maskName($buyerName) : $buyerName,
            'is_anonymous' => (bool) $review->is_anonymous,
            'buyer_photo_url' => $review->buyer?->profile_photo_path
                ? Storage::disk('public')->url($review->buyer->profile_photo_path)
                : null,
            'photo_url' => $review->photo_path ? Storage::disk('public')->url($review->photo_path) : null,
            'created_at' => $review->created_at?->toISOString(),
        ];
    }

    private function maskName(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if (! $parts) return 'Anonymous buyer';
        if (count($parts) === 1) return Str::substr($parts[0], 0, 1).str_repeat('*', max(1, Str::length($parts[0]) - 1));

        $first = $parts[0];
        $last = $parts[array_key_last($parts)];
        return Str::substr($first, 0, 1).str_repeat('*', max(1, Str::length($first) - 1))
            .' '.Str::substr($last, 0, 1).str_repeat('*', max(1, Str::length($last) - 1));
    }
}
