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
        'reviewed_by',
        'reviewed_at',
        'refund_amount',
        'refunded_at',
        'evidence',
        'complaint_id', 'problem_type', 'buyer_solution', 'pickup_date', 'pickup_time', 'pickup_address',
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

    public function events(): HasMany
    {
        return $this->hasMany(OrderReturnEvent::class)->orderBy('scanned_at');
    }
}
