<?php

namespace App\Models\Seller;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'name',
        'description',
        'category',
        'sku',
        'price',
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
        'price' => 'decimal:2',
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
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class)->orderBy('sort_order');
    }

    public function productSpecifications(): HasMany
    {
        return $this->hasMany(ProductSpecification::class)->orderBy('sort_order');
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
                'photo' => $option->photo,
                'photoData' => $option->photo,
            ])
            ->all();
    }
}
