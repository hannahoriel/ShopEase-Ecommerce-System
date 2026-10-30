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

        $data = $request->validate(['sale_percentage' => ['present', 'nullable', 'integer']]);
        if (! blank($data['sale_percentage']) && ! in_array((int) $data['sale_percentage'], range(10, 100), true)) {
            throw ValidationException::withMessages(['sale_percentage' => ['Choose a sale percentage from 10% to 100%.']]);
        }

        $product->sale_percentage = blank($data['sale_percentage']) ? null : (int) $data['sale_percentage'];
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
            'sale_price' => $product->sale_price,
            'status' => $product->status,
        ];
    }
}
