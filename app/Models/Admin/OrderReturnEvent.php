<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturnEvent extends Model
{
    protected $fillable = ['order_return_id', 'status', 'location', 'scanned_at'];

    protected $casts = ['scanned_at' => 'datetime'];

    public function orderReturn(): BelongsTo
    {
        return $this->belongsTo(OrderReturn::class);
    }
}
