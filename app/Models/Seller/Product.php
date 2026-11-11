<?php

namespace App\Models\Seller;

use App\Models\Admin\OrderItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'name',
        'description',
        'weight_kg',
        'is_fragile',
        'category',
        'sku',
        'price',
        'sale_percentage',
        'sale_discount_type',
        'sale_discount_value',
        'sale_starts_at',
        'sale_ends_at',
        'pricing_mode',
        'pricing_source',
        'photos',
        'stock_quantity',
        'status',
        'is_archived',
        'archived_by_admin',
        'archive_reason',
        'warning_reason',
        'warning_details',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:3',
        'is_fragile' => 'boolean',
        'price' => 'decimal:2',
        'sale_percentage' => 'integer',
        'sale_discount_value' => 'decimal:2',
        'sale_starts_at' => 'datetime',
        'sale_ends_at' => 'datetime',
        'stock_quantity' => 'integer',
        'is_archived' => 'boolean',
        'archived_by_admin' => 'boolean',
        'photos' => 'array',
    ];

    protected $appends = [
        'variations',
        'colors',
        'sizes',
        'specifications',
        'connected_variants',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function getSalePriceAttribute(): float
    {
        return $this->salePriceForBase((float) $this->price);
    }

    public function saleIsActive(): bool
    {
        $now = now();
        return ($this->sale_discount_value || $this->sale_percentage)
            && (! $this->sale_starts_at || $this->sale_starts_at->lte($now))
            && (! $this->sale_ends_at || $this->sale_ends_at->gte($now));
    }

    public function salePriceForBase(float $price): float
    {
        if (! $this->saleIsActive()) return round($price, 2);
        $type = $this->sale_discount_type ?: 'percentage';
        $value = (float) ($this->sale_discount_value ?? $this->sale_percentage ?? 0);
        return round(max(0, $type === 'fixed' ? $price - $value : $price * (100 - min(100, $value)) / 100), 2);
    }

    public function saleDiscountLabel(): ?string
    {
        if (! $this->saleIsActive()) return null;
        return ($this->sale_discount_type === 'fixed' ? '₱' . number_format((float) $this->sale_discount_value, 2) : (int) ($this->sale_discount_value ?? $this->sale_percentage) . '%') . ' off';
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class)->orderBy('sort_order');
    }

    public function productSpecifications(): HasMany
    {
        return $this->hasMany(ProductSpecification::class)->orderBy('sort_order');
    }

    public function connectedVariants(): HasMany
    {
        return $this->hasMany(ProductVariantCombination::class)->orderBy('sort_order');
    }

    public function getConnectedVariantsAttribute(): array
    {
        return $this->connectedVariants()
            ->get()
            ->map(fn (ProductVariantCombination $variant) => [
                ...($variant->choices ?? []),
                'id' => $variant->id,
                'choices' => $variant->choices ?? [],
                'pricing_mode' => $variant->pricing_mode,
                'pricing_source' => $variant->pricing_source,
                'base_price' => (float) $variant->base_price,
                'additions' => $variant->additions ?? [],
                'additional_price' => (float) $variant->additional_price,
                'final_price' => (float) $variant->final_price,
                'stock' => $variant->stock,
                'available' => $variant->available,
                'image_url' => $this->publicOptionPhoto($variant->photo) ?: $this->selectedOptionPhoto($variant->choices ?? []),
                'photo' => $this->publicOptionPhoto($variant->photo) ?: $this->selectedOptionPhoto($variant->choices ?? []),
            ])
            ->all();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getVariationsAttribute(): array
    {
        return $this->optionPayload('variation');
    }

    public function getColorsAttribute(): array
    {
        return $this->optionPayload('color');
    }

    public function getSizesAttribute(): array
    {
        return $this->optionPayload('size');
    }

    public function getSpecificationsAttribute(): array
    {
        return $this->productSpecifications()
            ->get()
            ->mapWithKeys(fn (ProductSpecification $specification) => [
                $specification->key => $specification->value,
            ])
            ->all();
    }

    public function publicOptionPhoto(?string $photo): ?string
    {
        if (! is_string($photo) || trim($photo) === '') return null;
        $value = trim($photo);
        if (preg_match('~^(https?://|data:|blob:)~i', $value)) return $value;
        $value = ltrim(preg_replace('~^(?:/)?(?:storage/|public/)~', '', $value), '/');
        return $value !== '' ? Storage::disk('public')->url($value) : null;
    }

    public function selectedOptionPhoto(array $choices): ?string
    {
        // Color photos are usually the buyer's most specific visual choice.
        foreach (['color' => 'colors', 'variation' => 'variations', 'size' => 'sizes'] as $type => $plural) {
            $name = $choices[$plural] ?? $choices[$type] ?? null;
            if (! is_scalar($name) || (string) $name === '') continue;
            $option = $this->options()->where('type', $type)->where('name', (string) $name)->first();
            if ($option && $option->photo) return $this->publicOptionPhoto($option->photo);
        }
        return null;
    }

    public function selectedImageForCart(?string $variation, ?string $color, ?string $size): ?string
    {
        $combination = $this->selectedCombinationForCart($variation, $color, $size);
        $choices = ['variations' => $variation, 'colors' => $color, 'sizes' => $size];

        return $this->publicOptionPhoto($combination?->photo)
            ?: $this->selectedOptionPhoto($choices);
    }

    public function selectedCombinationForCart(?string $variation, ?string $color, ?string $size): ?ProductVariantCombination
    {
        $selected = [
            'variations' => $variation,
            'colors' => $color,
            'sizes' => $size,
        ];

        $variants = $this->relationLoaded('connectedVariants')
            ? $this->getRelation('connectedVariants')
            : $this->connectedVariants()->get();

        return $variants->first(function (ProductVariantCombination $variant) use ($selected): bool {
            $choices = $variant->choices ?? [];

            foreach ($selected as $plural => $value) {
                $singular = match ($plural) {
                    'variations' => 'variation',
                    'colors' => 'color',
                    default => 'size',
                };
                $saved = $choices[$plural] ?? $choices[$singular] ?? null;

                if ((string) ($saved ?? '') !== (string) ($value ?? '')) {
                    return false;
                }
            }

            return true;
        });
    }

    public function salePriceForCart(?string $variation, ?string $color, ?string $size): float
    {
        $combination = $this->selectedCombinationForCart($variation, $color, $size);
        $price = $combination ? (float) $combination->final_price : (float) $this->price;
        return $this->salePriceForBase($price);
    }

    private function optionPayload(string $type): array
    {
        return $this->options()
            ->where('type', $type)
            ->get()
            ->map(fn (ProductOption $option) => [
                'name' => $option->name,
                'price' => (float) $option->price,
                'stock' => $option->stock,
                'price_type' => $option->price_type,
                'priceType' => $option->price_type,
                'photo' => $this->publicOptionPhoto($option->photo),
                'photoData' => $this->publicOptionPhoto($option->photo),
                'image_url' => $this->publicOptionPhoto($option->photo),
            ])
            ->all();
    }
}
