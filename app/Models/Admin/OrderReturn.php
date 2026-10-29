<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderReturn extends Model
{
    protected $fillable = [
        'order_id',
        'return_type',
        'status',
        'shipping_status',
        'courier',
        'tracking_number',
        'reason',
        'solution',
        'evidence_description',
        'return_items',
        'requested_at',
        'returned_at',
    ];

    protected $casts = [
        'return_items' => 'array',
        'requested_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(OrderReturnEvent::class)->orderBy('scanned_at');
    }
}
