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
            ->with('product.seller')
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

        $request->user()->buyerLikes()->firstOrCreate([
            'product_id' => $product->id,
        ]);

        return response()->json([
            'message' => 'Product added to your likes.',
            'data' => $this->productPayload($product->load('seller')),
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
        $image = is_array($photos) ? ($photos[0] ?? null) : null;
        if ($image && ! str_starts_with($image, 'http')) {
            $image = \Illuminate\Support\Facades\Storage::disk('public')->url($image);
        }

        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'image' => $image,
            'seller' => $product->seller?->store_name ?? 'ShopEase Seller',
            'url' => '/buyer/product?id='.$product->id,
        ];
    }
}
