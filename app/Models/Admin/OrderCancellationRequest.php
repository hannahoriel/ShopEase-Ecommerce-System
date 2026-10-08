<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderCancellationRequest extends Model
{
    use HasFactory;

    public const BUYER_REASONS = [
        'changed_mind',
        'ordered_by_mistake',
        'wrong_address',
        'delivery_too_slow',
        'payment_issue',
        'other',
    ];

    public const SELLER_REJECTION_REASONS = [
        'already_being_prepared',
        'order_ready_for_shipment',
        'unable_to_stop_fulfillment',
        'other',
    ];

    protected $fillable = [
        'order_id',
        'buyer_id',
        'status',
        'buyer_reason',
        'buyer_other_reason',
        'auto_approved',
        'decided_by',
        'seller_reason',
        'seller_other_reason',
        'requested_at',
        'decided_at',
    ];

    protected $casts = [
        'auto_approved' => 'boolean',
        'requested_at' => 'datetime',
        'decided_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
