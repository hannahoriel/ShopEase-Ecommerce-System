<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Seller\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $likes = $request->user()->buyerLikes()
            ->with(['product.seller.user', 'product.reviews'])
            ->latest('buyer_likes.created_at')
            ->get()
            ->map(fn ($like): array => $this->productPayload($like->product))
            ->values();

        return response()->json(['data' => $likes]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);
        $product = Product::query()
            ->whereKey($data['product_id'])
            ->where('status', 'active')
            ->where('is_archived', false)
            ->firstOrFail();
        $product->load(['seller.user', 'reviews']);

        $request->user()->buyerLikes()->firstOrCreate([
            'product_id' => $product->id,
        ]);

        return response()->json([
            'message' => 'Product added to your likes.',
            'data' => $this->productPayload($product),
        ], 201);
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $request->user()->buyerLikes()->where('product_id', $product->id)->delete();

        return response()->json(['message' => 'Product removed from your likes.']);
    }

    private function productPayload(?Product $product): array
    {
        if (! $product) return [];

        $photos = $product->photos ?? [];
        $image = is_array($photos)
            ? collect($photos)->first(fn ($photo) => is_string($photo) && trim($photo) !== null && trim($photo) !== '')
            : null;
        if (is_string($image)) {
            $image = trim($image);
            if ($image !== '' && ! preg_match('/^(https?:\/\/|data:)/i', $image)) {
                $image = preg_replace('/^(?:storage\/|public\/)+/i', '', ltrim($image, '/\\'));
                $image = \Illuminate\Support\Facades\Storage::disk('public')->url($image);
            }
        }
        $reviews = $product->relationLoaded('reviews') ? $product->reviews : collect();
        $ratingCount = $reviews->count();

        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'image' => $image,
            'seller' => $product->seller?->store_name ?: ($product->seller?->business_name ?: ($product->seller?->user?->name ?? 'ShopEase Seller')),
            'rating' => $ratingCount ? round((float) $reviews->avg('rating'), 1) : 0.0,
            'rating_count' => $ratingCount,
            'url' => '/buyer/product?id='.$product->id,
        ];
    }
}
