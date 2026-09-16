<?php

namespace App\Models\Seller;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'name',
        'sku',
        'price',
        'stock_quantity',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }
}
