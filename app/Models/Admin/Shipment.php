<?php

namespace App\Models\Admin;

use App\Models\Rider\Rider;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsDeliveryHub;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'rider_id',
        'logistics_branch_id',
        'logistics_delivery_hub_id',
        'tracking_number',
        'scan_token',
        'courier',
        'estimated_delivery',
        'shipping_fee',
        'picked_up_at',
        'delivered_at',
        'current_location',
        'current_latitude',
        'current_longitude',
        'location_updated_at',
    ];

    protected $casts = [
        'estimated_delivery' => 'date',
        'shipping_fee' => 'decimal:2',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
        'current_latitude' => 'float',
        'current_longitude' => 'float',
        'location_updated_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    public function logisticsBranch(): BelongsTo
    {
        return $this->belongsTo(LogisticsBranch::class);
    }

    public function deliveryHub(): BelongsTo
    {
        return $this->belongsTo(LogisticsDeliveryHub::class, 'logistics_delivery_hub_id');
    }

    public function scans(): HasMany
    {
        return $this->hasMany(ShipmentScan::class)->latest('scanned_at');
    }
}
