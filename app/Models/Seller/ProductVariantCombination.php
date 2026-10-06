<?php

namespace App\Models\Seller;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariantCombination extends Model
{
    protected $fillable = [
        'product_id',
        'choices',
        'pricing_mode',
        'pricing_source',
        'base_price',
        'additions',
        'additional_price',
        'final_price',
        'stock',
        'available',
        'sort_order',
    ];

    protected $casts = [
        'choices' => 'array',
        'additions' => 'array',
        'base_price' => 'decimal:2',
        'additional_price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'stock' => 'integer',
        'available' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
