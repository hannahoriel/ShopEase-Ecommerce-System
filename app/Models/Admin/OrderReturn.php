<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Rider\Rider;

class OrderReturn extends Model
{
    public const MAX_PICKUP_ATTEMPTS = 3;

    public const PICKUP_TIMES = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00'];

    public function canSchedulePickup(bool $assignRider = false): bool
    {
        return $this->complaint_id && $this->status === 'accepted' && $this->return_type === 'return'
            && (int) $this->failed_pickup_attempts < self::MAX_PICKUP_ATTEMPTS
            && (! $this->pickup_date || $this->shipping_status === 'pickup_failed'
                || ($assignRider && $this->shipping_status === 'scheduled' && ! $this->pickup_rider_id));
    }

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
        'reviewed_by',
        'reviewed_at',
        'refund_amount',
        'refunded_at',
        'evidence',
        'complaint_id', 'problem_type', 'buyer_solution', 'pickup_date', 'pickup_time', 'pickup_address',
        'return_delivery_hub_id',
        'delivery_rider_id',
    ];

    protected $casts = [
        'return_items' => 'array',
        'requested_at' => 'datetime',
        'returned_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'refund_amount' => 'decimal:2',
        'evidence' => 'array',
        'pickup_date' => 'date',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function complaint(): BelongsTo { return $this->belongsTo(Complaint::class); }

    public function pickupRider(): BelongsTo
    {
        return $this->belongsTo(Rider::class, 'pickup_rider_id');
    }

    public function returnDeliveryHub(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Logistics\LogisticsDeliveryHub::class, 'return_delivery_hub_id');
    }

    public function deliveryRider(): BelongsTo
    {
        return $this->belongsTo(Rider::class, 'delivery_rider_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(OrderReturnEvent::class)->orderBy('scanned_at');
    }
}
