<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SalesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $seller = Seller::query()->where('user_id', $request->user()->id)->firstOrFail();
        $products = $seller->products()->where('is_archived', false)->latest()->get();

        return response()->json(['data' => $products->map(fn (Product $product) => $this->present($product))->values()]);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $seller = Seller::query()->where('user_id', $request->user()->id)->firstOrFail();
        abort_unless((int) $product->seller_id === (int) $seller->id, 404);

        $data = $request->validate([
            'discount_type' => ['nullable', 'in:percentage,fixed'],
            'discount_value' => ['nullable', 'numeric', 'gt:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);
        if (blank($data['discount_type'] ?? null) || blank($data['discount_value'] ?? null)) {
            $product->sale_discount_type = null;
            $product->sale_discount_value = null;
            $product->sale_starts_at = null;
            $product->sale_ends_at = null;
            $product->sale_percentage = null;
        } else {
            if ($data['discount_type'] === 'percentage' && (float) $data['discount_value'] > 100) {
                throw ValidationException::withMessages(['discount_value' => ['Percentage discount cannot exceed 100%.']]);
            }
            if ($data['discount_type'] === 'fixed' && (float) $data['discount_value'] >= (float) $product->price) {
                throw ValidationException::withMessages(['discount_value' => ['Fixed discount must be less than the product price.']]);
            }
            $product->sale_discount_type = $data['discount_type'];
            $product->sale_discount_value = $data['discount_value'];
            $product->sale_starts_at = $data['starts_at'] ?? null;
            $product->sale_ends_at = $data['ends_at'] ?? null;
            $product->sale_percentage = $data['discount_type'] === 'percentage' ? (int) $data['discount_value'] : null;
        }
        $product->save();

        return response()->json(['data' => $this->present($product->fresh())]);
    }

    private function present(Product $product): array
    {
        $photos = collect($product->photos ?? [])->map(function ($photo): ?string {
            if (! is_string($photo) || trim($photo) === '') return null;
            if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://') || str_starts_with($photo, 'data:')) return $photo;
            return \Illuminate\Support\Facades\Storage::disk('public')->url(ltrim(preg_replace('/^storage\//', '', $photo), '/'));
        })->filter()->values();

        return [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'image_url' => $photos->first(),
            'price' => (float) $product->price,
            'sale_percentage' => $product->sale_percentage,
            'discount_type' => $product->sale_discount_type,
            'discount_value' => $product->sale_discount_value,
            'starts_at' => $product->sale_starts_at,
            'ends_at' => $product->sale_ends_at,
            'sale_active' => $product->saleIsActive(),
            'sale_price' => $product->sale_price,
            'stock' => $product->stock_quantity,
            'status' => $product->status,
        ];
    }
}
