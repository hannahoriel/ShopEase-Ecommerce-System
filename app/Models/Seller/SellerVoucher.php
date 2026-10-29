<?php

namespace App\Models\Seller;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SellerVoucher extends Model
{
    protected $fillable = [
        'seller_id', 'code', 'title', 'discount_type', 'discount_value', 'minimum_spend',
        'maximum_discount', 'product_ids', 'eligible_buyers', 'stacking', 'quantity',
        'claimed', 'per_buyer', 'starts_at', 'ends_at', 'terms', 'enabled',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2', 'minimum_spend' => 'decimal:2',
        'maximum_discount' => 'decimal:2', 'product_ids' => 'array',
        'starts_at' => 'datetime', 'ends_at' => 'datetime', 'enabled' => 'boolean',
        'quantity' => 'integer', 'claimed' => 'integer', 'per_buyer' => 'integer',
    ];

    public function seller(): BelongsTo { return $this->belongsTo(Seller::class); }
    public function redemptions(): HasMany { return $this->hasMany(SellerVoucherRedemption::class); }
}
