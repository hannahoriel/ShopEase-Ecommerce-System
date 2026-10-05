<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Buyer\CartItem;
use App\Models\Seller\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /** GET /api/v1/buyer/cart — list all cart items for the authenticated buyer */
    public function index(Request $request): JsonResponse
    {
        $items = CartItem::with(['product.seller', 'product.options', 'product.productSpecifications'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'data'  => $items->map(fn (CartItem $item) => $this->serialize($item))->values(),
            'total' => $items->count(),
        ]);
    }

    /** POST /api/v1/buyer/cart — add or increment a product in the cart */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['sometimes', 'integer', 'min:1', 'max:99'],
            'variation'  => ['nullable', 'string', 'max:100'],
            'color'      => ['nullable', 'string', 'max:100'],
            'size'       => ['nullable', 'string', 'max:100'],
        ]);

        $product = Product::where('id', $validated['product_id'])
            ->where('status', 'active')
            ->where('is_archived', false)
            ->whereHas('seller', fn ($q) => $q->where('registration_status', 'active'))
            ->firstOrFail();

        $qty = (int) ($validated['quantity'] ?? 1);

        $item = CartItem::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->where('variation', $validated['variation'] ?? null)
            ->where('color', $validated['color'] ?? null)
            ->where('size', $validated['size'] ?? null)
            ->first();

        if ($item) {
            $item->quantity = min(99, $item->quantity + $qty);
            $item->save();
        } else {
            $item = CartItem::create([
                'user_id'    => $request->user()->id,
                'product_id' => $product->id,
                'quantity'   => $qty,
                'variation'  => $validated['variation'] ?? null,
                'color'      => $validated['color'] ?? null,
                'size'       => $validated['size'] ?? null,
            ]);
        }

        $item->load(['product.seller', 'product.options', 'product.productSpecifications']);

        return response()->json($this->serialize($item), 201);
    }

    /** PATCH /api/v1/buyer/cart/{item} — update quantity of a specific cart item */
    public function update(Request $request, CartItem $item): JsonResponse
    {
        abort_unless($item->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $item->update(['quantity' => $validated['quantity']]);

        $item->load(['product.seller', 'product.options', 'product.productSpecifications']);

        return response()->json($this->serialize($item));
    }

    /** DELETE /api/v1/buyer/cart/{item} — remove a single cart item */
    public function destroy(Request $request, CartItem $item): JsonResponse
    {
        abort_unless($item->user_id === $request->user()->id, 403);

        $item->delete();

        return response()->json(null, 204);
    }

    /** DELETE /api/v1/buyer/cart — clear the entire cart */
    public function clear(Request $request): JsonResponse
    {
        CartItem::where('user_id', $request->user()->id)->delete();

        return response()->json(null, 204);
    }

    private function serialize(CartItem $item): array
    {
        $product = $item->product;
        $photos  = $this->resolvePhotos($product->photos ?? []);

        return [
            'id'        => $item->id,
            'quantity'  => $item->quantity,
            'variation' => $item->variation,
            'color'     => $item->color,
            'size'      => $item->size,
            'product'   => [
                'id'           => $product->id,
                'name'         => $product->name,
                'price'        => (float) $product->price,
                'pricing_mode' => $product->pricing_mode,
                'stock'        => $product->stock_quantity,
                'category'     => $product->category,
                'image_url'    => $photos[0] ?? null,
                'photos'       => $photos,
                'variations'   => $product->variations,
                'colors'       => $product->colors,
                'sizes'        => $product->sizes,
                'seller'       => [
                    'id'         => $product->seller->id,
                    'store_name' => $product->seller->store_name,
                ],
            ],
        ];
    }

    private function resolvePhotos(array $photos): array
    {
        return array_values(array_filter(array_map(function ($photo): ?string {
            if (!is_string($photo) || trim($photo) === '') {
                return null;
            }
            $value = trim($photo);
            if (str_starts_with($value, 'data:') || str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
                return $value;
            }
            $path = ltrim(preg_replace('/^storage\//', '', $value), '/');
            return $path !== '' ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : null;
        }, $photos)));
    }
}
