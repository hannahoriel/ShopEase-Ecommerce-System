<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'tracking_number',
        'scan_token',
        'courier',
        'estimated_delivery',
        'shipping_fee',
        'picked_up_at',
        'delivered_at',
        'current_location',
    ];

    protected $casts = [
        'estimated_delivery' => 'date',
        'shipping_fee' => 'decimal:2',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scans(): HasMany
    {
        return $this->hasMany(ShipmentScan::class)->latest('scanned_at');
    }
}
