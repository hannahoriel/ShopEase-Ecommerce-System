<?php

namespace App\Models\Seller;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'name',
        'price',
        'stock',
        'price_type',
        'photo',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
