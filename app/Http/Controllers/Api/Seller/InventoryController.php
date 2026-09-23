<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $seller = $this->sellerFor($request->user());
        $query = Product::query()->where('seller_id', $seller->id)
            ->where('is_archived', $request->boolean('archived', false));

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());
            $query->where(function ($productQuery) use ($search): void {
                $productQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        return response()->json($query->latest()->paginate($request->integer('per_page', 10)));
    }

    public function store(Request $request): JsonResponse
    {
        $seller = $this->sellerFor($request->user());
        $validated = $request->validate($this->rules($seller->id));
        $optionGroups = [
            'variation' => $this->optionItems($request->input('variation_items', [])),
            'color' => $this->optionItems($request->input('color_items', [])),
            'size' => $this->optionItems($request->input('size_items', [])),
        ];
        foreach ($optionGroups as $type => &$items) {
            foreach ($items as $index => &$item) {
                $photo = $request->file("{$type}_items.{$index}.photo");

                if ($photo) {
                    $item['photo'] = $photo->store("products/{$type}s", 'public');
                }
            }
            unset($item);
        }
        unset($items);
        $specifications = $request->input('category_specifications', []);

        if (request()->routeIs('seller.inventory.products.store')) {
            $validated['name'] = $request->input('title');
            $validated['stock_quantity'] = $request->input('stock');
            $validated['status'] = 'active';
        }
        $validated['photos'] = collect($request->file('product_photos', []))
            ->map(fn ($photo) => $photo->store('products', 'public'))
            ->values()->all();
        $pricingGroup = match ($validated['pricing_source'] ?? null) {
            'variations' => 'variation',
            'colors' => 'color',
            'sizes' => 'size',
            default => 'variation',
        };
        $baseItem = collect($optionGroups[$pricingGroup] ?? [])
            ->firstWhere('price_type', 'base');
        $validated['price'] = $request->input('price') ?? ($baseItem['price'] ?? 0);

        unset(
            $validated['title'],
            $validated['stock'],
            $validated['variation_items'],
            $validated['color_items'],
            $validated['size_items'],
            $validated['category_specifications']
        );

        $product = $seller->products()->create($validated);

        foreach ($optionGroups as $type => $items) {
            $product->options()->createMany(
                collect($items)->map(fn (array $item, int $index) => [
                    ...$item,
                    'type' => $type,
                    'sort_order' => $index,
                ])->all()
            );
        }

        $product->productSpecifications()->createMany(
            collect($specifications)->map(fn ($value, $key) => [
                'key' => preg_replace('/^category_specifications\[|\]$/', '', (string) $key),
                'value' => is_scalar($value) ? (string) $value : json_encode($value),
            ])->values()->all()
        );

        return response()->json($product->fresh(), 201);
    }

    public function show(Request $request, Product $product): JsonResponse
    {
        return response()->json($this->ownedProduct($request->user(), $product));
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $product = $this->ownedProduct($request->user(), $product);
        $validated = $request->validate($this->rules($product->seller_id, $product->id, true));
        $product->update($validated);

        return response()->json($product->fresh());
    }

    public function archive(Request $request, Product $product): JsonResponse
    {
        $product = $this->ownedProduct($request->user(), $product);
        $product->update(['is_archived' => true]);

        return response()->json($product->fresh());
    }

    public function unarchive(Request $request, Product $product): JsonResponse
    {
        $product = $this->ownedProduct($request->user(), $product);
        $product->update(['is_archived' => false]);

        return response()->json($product->fresh());
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $this->ownedProduct($request->user(), $product)->delete();

        return response()->json(null, 204);
    }

    private function rules(int $sellerId, ?int $productId = null, bool $partial = false): array
    {
        $required = $partial ? ['sometimes'] : ['required'];
        $uiPayload = request()->routeIs('seller.inventory.products.store');

        return [
            'name' => $uiPayload ? ['nullable', 'string', 'max:255'] : [...$required, 'string', 'max:255'],
            'title' => [$uiPayload && !$partial ? 'required' : 'nullable', 'string', 'max:120'],
            'description' => [$uiPayload && !$partial ? 'required' : 'nullable', 'string', 'max:2000'],
            'category' => [$uiPayload && !$partial ? 'required' : 'nullable', 'string', 'max:100'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->where(fn ($query) => $query->where('seller_id', $sellerId))->ignore($productId)],
            'price' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'stock' => [$uiPayload && !$partial ? 'required' : 'nullable', 'integer', 'min:0'],
            'pricing_mode' => ['nullable', Rule::in(['fixed', 'varies'])],
            'pricing_source' => ['nullable', Rule::in(['variations', 'colors', 'sizes'])],
            'variation_items' => ['nullable', 'array'],
            'variation_items.*.name' => ['required_with:variation_items', 'string', 'max:120'],
            'variation_items.*.price' => ['nullable', 'numeric', 'min:0'],
            'variation_items.*.stock' => ['required_with:variation_items', 'integer', 'min:0'],
            'variation_items.*.photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'color_items' => ['nullable', 'array'],
            'color_items.*.name' => ['required_with:color_items', 'string', 'max:120'],
            'color_items.*.price' => ['nullable', 'numeric', 'min:0'],
            'color_items.*.stock' => ['required_with:color_items', 'integer', 'min:0'],
            'color_items.*.photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'size_items' => ['nullable', 'array'],
            'size_items.*.name' => ['required_with:size_items', 'string', 'max:120'],
            'size_items.*.price' => ['nullable', 'numeric', 'min:0'],
            'size_items.*.stock' => ['required_with:size_items', 'integer', 'min:0'],
            'size_items.*.photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'category_specifications' => ['nullable', 'array'],
            'product_photos' => [$uiPayload && !$partial ? 'required' : 'nullable', 'array', 'min:1', 'max:8'],
            'product_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'status' => ['nullable', Rule::in(['active', 'inactive', 'pending'])],
        ];
    }

    private function optionItems(array $items): array
    {
        return collect($items)->map(fn (array $item) => [
            'name' => trim((string) ($item['name'] ?? '')),
            'price' => (float) ($item['price'] ?? 0),
            'stock' => (int) ($item['stock'] ?? 0),
            'price_type' => $item['price_type'] ?? 'addon',
        ])->filter(fn (array $item) => $item['name'] !== '')->values()->all();
    }

    private function sellerFor(User $user): Seller
    {
        return Seller::where('user_id', $user->id)->firstOrFail();
    }

    private function ownedProduct(User $user, Product $product): Product
    {
        abort_unless($product->seller_id === $this->sellerFor($user)->id, 404);

        return $product;
    }
}
