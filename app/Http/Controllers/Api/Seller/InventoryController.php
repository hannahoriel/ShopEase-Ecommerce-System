<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller\Product;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;
use Throwable;

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
        $optionGroups = [
            'variations' => ['input' => 'variation_items', 'type' => 'variation'],
            'colors' => ['input' => 'color_items', 'type' => 'color'],
            'sizes' => ['input' => 'size_items', 'type' => 'size'],
        ];
        $pricingModeInput = $request->input('pricing_mode', 'fixed');
        $pricingMode = is_string($pricingModeInput) && $pricingModeInput !== ''
            ? $pricingModeInput
            : 'fixed';
        $pricingSourceInput = $request->input('pricing_source');
        $pricingSource = is_string($pricingSourceInput) ? $pricingSourceInput : null;

        $rawCombinations = $request->input('variant_combinations', []);
        if (is_string($rawCombinations)) {
            $decodedCombinations = json_decode($rawCombinations, true);
            $request->merge([
                'variant_combinations' => json_last_error() === JSON_ERROR_NONE
                    ? $decodedCombinations
                    : $rawCombinations,
            ]);
        }

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

        if ($pricingMode === 'varies' && isset($optionGroups[$pricingSource])) {
            $sourceOptions = $request->input($optionGroups[$pricingSource]['input'], []);
            if (is_array($sourceOptions)) {
                $baseOption = collect($sourceOptions)->first(
                    fn ($option) => is_array($option)
                        && isset($option['price'])
                        && is_numeric($option['price'])
                );

                if ($baseOption !== null) {
                    $request->merge(['price' => $baseOption['price']]);
                }

                $request->merge([
                    'stock_quantity' => collect($sourceOptions)->sum(
                        fn ($option) => is_array($option) && is_numeric($option['stock'] ?? null)
                            ? (int) $option['stock']
                            : 0
                    ),
                ]);
            }
        }

        $submittedCombinations = $request->input('variant_combinations', []);
        if (is_array($submittedCombinations) && $submittedCombinations !== []) {
            $request->merge([
                'stock_quantity' => collect($submittedCombinations)->sum(
                    fn ($combination) => is_array($combination) && is_numeric($combination['stock'] ?? null)
                        ? (int) $combination['stock']
                        : 0
                ),
            ]);
        }

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'sku' => [
                'nullable',
                'string',
                'max:80',
                Rule::unique('products', 'sku')->where(fn ($query) => $query->where('seller_id', $seller->id)),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => [Rule::requiredIf($pricingMode !== 'varies'), 'nullable', 'numeric', 'min:0'],
            'stock_quantity' => [Rule::requiredIf($pricingMode !== 'varies'), 'nullable', 'integer', 'min:0'],
            'pricing_mode' => ['nullable', Rule::in(['fixed', 'varies'])],
            'pricing_source' => [Rule::requiredIf($pricingMode === 'varies'), 'nullable', Rule::in(['variations', 'colors', 'sizes'])],
            'category' => ['required', 'string', 'max:255'],
            'photos' => ['required_without:product_photos', 'array', 'min:1', 'max:8'],
            'photos.*' => ['required', 'string'],
            'product_photos' => ['required_without:photos', 'array', 'min:1', 'max:8'],
            'product_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'category_specifications' => ['sometimes', 'array'],
            'category_specifications.*' => ['nullable', 'string', 'max:2000'],
            'variant_combinations' => ['sometimes', 'array', 'max:300'],
            'variant_combinations.*' => ['array'],
            'variant_combinations.*.pricing_mode' => ['required', Rule::in(['fixed', 'varies'])],
            'variant_combinations.*.pricing_source' => ['nullable', Rule::in(['variations', 'colors', 'sizes'])],
            'variant_combinations.*.base_price' => ['required', 'numeric', 'min:0'],
            'variant_combinations.*.additions' => ['sometimes', 'array'],
            'variant_combinations.*.additions.*' => ['numeric', 'min:0'],
            'variant_combinations.*.additional_price' => ['required', 'numeric', 'min:0'],
            'variant_combinations.*.final_price' => ['required', 'numeric', 'min:0'],
            'variant_combinations.*.stock' => ['required', 'integer', 'min:0'],
            'variant_combinations.*.available' => ['sometimes', 'boolean'],
        ];

        foreach ($optionGroups as $source => $group) {
            $isRequiredPricingGroup = $pricingMode === 'varies' && $pricingSource === $source;
            $rules["variant_combinations.*.{$source}"] = ['sometimes', 'string', 'max:120'];
            $rules[$group['input']] = $isRequiredPricingGroup
                ? ['required', 'array', 'min:1']
                : ['sometimes', 'array'];
            $rules[$group['input'].'.*.name'] = ['required', 'string', 'max:120', 'distinct'];
            $rules[$group['input'].'.*.price'] = ['required', 'numeric', 'min:0'];
            $rules[$group['input'].'.*.stock'] = ['required', 'integer', 'min:0'];
            $rules[$group['input'].'.*.price_type'] = ['nullable', Rule::in(['base', 'addon'])];
            $rules[$group['input'].'.*.photo'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
        }

        $validated = $request->validate($rules);
        $connectedVariants = $this->buildConnectedVariants(
            $validated['variant_combinations'] ?? [],
            $validated,
            $optionGroups,
            $pricingMode,
            $pricingSource
        );
        if ($connectedVariants !== []) {
            if ($pricingMode === 'varies' && isset($optionGroups[$pricingSource])) {
                $sourceInput = $optionGroups[$pricingSource]['input'];

                foreach ($connectedVariants as $variant) {
                    $sourceChoice = $variant['choices'][$pricingSource];

                    foreach ($validated[$sourceInput] as &$sourceOption) {
                        if ($sourceOption['name'] === $sourceChoice) {
                            $sourceOption['price'] = $variant['base_price'];
                            break;
                        }
                    }
                    unset($sourceOption);
                }
            }

            $validated['price'] = min(array_column($connectedVariants, 'final_price'));
        }
        $productPhotos = array_values(array_filter(
            $validated['photos'] ?? [],
            fn ($photo) => is_string($photo) && trim($photo) !== ''
        ));
        $storedFiles = [];

        try {
            $product = DB::transaction(function () use (
                $seller,
                $validated,
                $request,
                $optionGroups,
                $pricingMode,
                $pricingSource,
                $connectedVariants,
                $productPhotos,
                &$storedFiles
            ): Product {
                $photos = $productPhotos;

                if ($photos === []) {
                    foreach ($validated['product_photos'] ?? [] as $photo) {
                        $photos[] = $this->storeUploadedPhoto($photo, 'products', $storedFiles);
                    }
                }

                $product = $seller->products()->create([
                    'name' => $validated['name'],
                    'sku' => $validated['sku'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'price' => $validated['price'] ?? 0,
                    'pricing_mode' => $validated['pricing_mode'] ?? 'fixed',
                    'pricing_source' => $pricingMode === 'varies' ? $pricingSource : null,
                    'stock_quantity' => (int) ($validated['stock_quantity'] ?? 0),
                    'status' => 'pending',
                    'category' => $validated['category'],
                    'photos' => $photos,
                    'is_archived' => false,
                ]);

                $specificationOrder = 0;
                foreach ($validated['category_specifications'] ?? [] as $index => $value) {
                    $value = is_string($value) ? trim($value) : '';

                    if ($value === '') {
                        continue;
                    }

                    $product->productSpecifications()->create([
                        'key' => (string) $index,
                        'value' => $value,
                        'sort_order' => $specificationOrder++,
                    ]);
                }

                foreach ($optionGroups as $source => $group) {
                    foreach ($validated[$group['input']] ?? [] as $index => $option) {
                        $photo = $option['photo'] ?? null;

                        if ($photo instanceof UploadedFile) {
                            $photo = $this->storeUploadedPhoto($photo, 'product-options', $storedFiles);
                        }

                        $product->options()->create([
                            'type' => $group['type'],
                            'name' => $option['name'],
                            'price' => $pricingMode === 'fixed'
                                ? $validated['price']
                                : $option['price'],
                            'stock' => $option['stock'],
                            'price_type' => $pricingMode === 'varies' && $pricingSource === $source
                                ? 'base'
                                : 'addon',
                            'photo' => is_string($photo) ? $photo : null,
                            'sort_order' => $index,
                        ]);
                    }
                }

                foreach ($connectedVariants as $index => $variant) {
                    $product->connectedVariants()->create([
                        ...$variant,
                        'sort_order' => $index,
                    ]);
                }

                return $product;
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedFiles);
            throw $exception;
        }

        return response()->json($this->serializeProduct($product->fresh()), 201);
    }

    private function buildConnectedVariants(
        array $combinations,
        array $validated,
        array $optionGroups,
        string $pricingMode,
        ?string $pricingSource
    ): array {
        if ($combinations === []) {
            return [];
        }

        $activeGroups = [];
        foreach ($optionGroups as $source => $group) {
            if (!empty($validated[$group['input']])) {
                $activeGroups[$source] = $validated[$group['input']];
            }
        }

        $allowedKeys = array_merge(
            array_keys($activeGroups),
            ['pricing_mode', 'pricing_source', 'base_price', 'additions', 'additional_price', 'final_price', 'stock', 'available']
        );
        $seen = [];
        $variants = [];

        foreach ($combinations as $index => $combination) {
            $choices = [];

            foreach ($activeGroups as $source => $options) {
                $choice = trim((string) ($combination[$source] ?? ''));
                $matchesOption = collect($options)->contains(
                    fn (array $option) => trim($option['name']) === $choice
                );

                if ($choice === '' || !$matchesOption) {
                    throw ValidationException::withMessages([
                        "variant_combinations.{$index}.{$source}" => "Choose a valid {$source} option for this connected variant.",
                    ]);
                }

                $choices[$source] = $choice;
            }

            foreach (array_keys($combination) as $key) {
                if (!in_array($key, $allowedKeys, true)) {
                    throw ValidationException::withMessages([
                        "variant_combinations.{$index}.{$key}" => 'This choice is not an available product option.',
                    ]);
                }
            }

            $combinationKey = json_encode($choices);
            if (isset($seen[$combinationKey])) {
                throw ValidationException::withMessages([
                    "variant_combinations.{$index}" => 'This exact connected variant has already been added.',
                ]);
            }
            $seen[$combinationKey] = true;

            $basePrice = (float) ($combination['base_price'] ?? $validated['price'] ?? 0);
            if ($pricingMode === 'varies') {
                if (!isset($activeGroups[$pricingSource])) {
                    throw ValidationException::withMessages([
                        "variant_combinations.{$index}.{$pricingSource}" => 'The pricing-source choice is not available.',
                    ]);
                }

                $baseOption = collect($activeGroups[$pricingSource])->first(
                    fn (array $option) => $option['name'] === $choices[$pricingSource]
                );

                if ($baseOption === null) {
                    throw ValidationException::withMessages([
                        "variant_combinations.{$index}.{$pricingSource}" => 'The selected base-price choice is not available.',
                    ]);
                }
            }

            $additions = [];
            $additionalPrice = 0.0;
            if ($pricingMode === 'varies') {
                foreach ($activeGroups as $source => $_options) {
                    if ($source === $pricingSource) {
                        continue;
                    }

                    $amount = (float) ($combination['additions'][$source] ?? 0);
                    $additions[$source] = $amount;
                    $additionalPrice += $amount;
                }
            }

            $variants[] = [
                'choices' => $choices,
                'pricing_mode' => $pricingMode,
                'pricing_source' => $pricingMode === 'varies' ? $pricingSource : null,
                'base_price' => $basePrice,
                'additions' => $additions,
                'additional_price' => $additionalPrice,
                'final_price' => $basePrice + $additionalPrice,
                'stock' => (int) $combination['stock'],
                'available' => (bool) ($combination['available'] ?? true),
            ];
        }

        return $variants;
    }

    private function storeUploadedPhoto(UploadedFile $photo, string $directory, array &$storedFiles): string
    {
        $path = $photo->store($directory, 'public');

        if (!is_string($path) || $path === '') {
            throw new \RuntimeException('Unable to store an uploaded product photo.');
        }

        $storedFiles[] = $path;

        return $path;
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
            'photos' => ['sometimes', 'array'],
            'photos.*' => ['nullable', 'string'],
            'is_archived' => ['sometimes', 'boolean'],
        ]);

        if (array_key_exists('photos', $validated)) {
            $validated['photos'] = $this->normalizePhotos($validated['photos']);
        }

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
        $data = $product->toArray();

        if (isset($data['photos'])) {
            $data['photos'] = $this->normalizePhotos($data['photos']);
        }

        if (!empty($data['photos'])) {
            $data['image_url'] = $data['photos'][0];
        }

        return $data;
    }

    protected function normalizePhotos(mixed $photos): array
    {
        if (is_string($photos)) {
            $decoded = json_decode($photos, true);
            $photos = is_array($decoded) ? $decoded : [$photos];
        }

        if (!is_array($photos)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($photo): ?string {
            if (!is_string($photo)) {
                return null;
            }

            $value = trim($photo);

            if ($value === '') {
                return null;
            }

            if (str_starts_with($value, 'data:') || str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
                return $value;
            }

            $relativePath = ltrim($value, '/');
            $relativePath = preg_replace('/^storage\//', '', $relativePath);

            if ($relativePath === '') {
                return null;
            }

            return Storage::disk('public')->url($relativePath);
        }, $photos), fn (?string $photo) => $photo !== null && $photo !== ''));
    }
}
