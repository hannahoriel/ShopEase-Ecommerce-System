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
        $seller = $this->sellerForAuthUser($request->user());
        $archived = $request->boolean('archived');

        $query = $seller->products()
            ->when(
                $archived,
                fn ($query) => $query->where('is_archived', true),
                fn ($query) => $query->where('is_archived', false)
            );

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->get();

        return response()->json([
            'total' => $products->count(),
            'data' => $products->map(fn (Product $product) => $this->serializeProduct($product))->values()->all(),
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        $seller = $this->sellerForAuthUser(request()->user());

        abort_unless($product->seller_id === $seller->id, 404);

        return response()->json($this->serializeProduct($product));
    }

    public function store(Request $request): JsonResponse
    {
        $seller = $this->sellerForAuthUser($request->user());

        if ($request->has('title') || $request->has('name')) {
            $request->merge([
                'name' => $request->input('name', $request->input('title')),
            ]);
        }

        if ($request->has('stock') || $request->has('stock_quantity')) {
            $request->merge([
                'stock_quantity' => $request->input('stock_quantity', $request->input('stock')),
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->where(fn ($query) => $query->where('seller_id', $seller->id)),
            ],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'status' => ['nullable', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'max:255'],
        ]);

        $product = $seller->products()->create([
            'name' => $validated['name'],
            'sku' => $validated['sku'] ?? null,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock_quantity' => (int) $validated['stock_quantity'],
            'status' => $validated['status'] ?? 'pending',
            'category' => $validated['category'] ?? null,
            'is_archived' => false,
        ]);

        return response()->json($this->serializeProduct($product), 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $seller = $this->sellerForAuthUser($request->user());

        abort_unless($product->seller_id === $seller->id, 404);

        if ($request->has('title') || $request->has('name')) {
            $request->merge([
                'name' => $request->input('name', $request->input('title')),
            ]);
        }

        if ($request->has('stock') || $request->has('stock_quantity')) {
            $request->merge([
                'stock_quantity' => $request->input('stock_quantity', $request->input('stock')),
            ]);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'sku' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->where(fn ($query) => $query->where('seller_id', $seller->id))->ignore($product->id),
            ],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'stock_quantity' => ['sometimes', 'integer', 'min:0'],
            'status' => ['sometimes', 'string', 'max:50'],
            'category' => ['sometimes', 'nullable', 'string', 'max:255'],
            'is_archived' => ['sometimes', 'boolean'],
        ]);

        $product->fill($validated);

        $product->save();

        return response()->json($this->serializeProduct($product->fresh()));
    }

    public function archive(Product $product): JsonResponse
    {
        $seller = $this->sellerForAuthUser(request()->user());

        abort_unless($product->seller_id === $seller->id, 404);

        if ($product->archived_by_admin) {
            return response()->json([
                'message' => 'This product was removed by an administrator and cannot be restored by the seller.',
            ], 403);
        }

        $product->update([
            'is_archived' => true,
            'status' => 'archived',
            'archived_by_admin' => false,
            'archive_reason' => null,
        ]);

        return response()->json($this->serializeProduct($product->fresh()));
    }

    public function unarchive(Product $product): JsonResponse
    {
        $seller = $this->sellerForAuthUser(request()->user());

        abort_unless($product->seller_id === $seller->id, 404);

        if ($product->archived_by_admin) {
            return response()->json([
                'message' => 'This product was removed by an administrator and cannot be restored by the seller.',
            ], 403);
        }

        $product->update([
            'is_archived' => false,
            'status' => 'active',
            'archive_reason' => null,
        ]);

        return response()->json($this->serializeProduct($product->fresh()));
    }

    public function destroy(Product $product): JsonResponse
    {
        $seller = $this->sellerForAuthUser(request()->user());

        abort_unless($product->seller_id === $seller->id, 404);

        $product->delete();

        return response()->json(null, 204);
    }

    protected function sellerForAuthUser(?User $user): Seller
    {
        abort_unless($user && $user->role === User::ROLE_SELLER, 403);

        return Seller::query()->where('user_id', $user->id)->firstOrFail();
    }

    protected function serializeProduct(Product $product): array
    {
        return $product->toArray();
    }
}
