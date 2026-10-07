<?php

namespace App\Models\Admin;

use App\Models\Seller\Seller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionTransaction extends Model
{
    public const DEFAULT_RATE = 10.0;

    protected $fillable = [
        'order_id',
        'seller_id',
        'order_amount',
        'commission_rate',
        'commission_amount',
        'status',
        'earned_at',
    ];

    protected $casts = [
        'order_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'earned_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public static function syncFromOrder(Order $order): self
    {
        $status = match ($order->status) {
            'completed' => 'earned',
            'cancelled', 'refunded' => 'reversed',
            default => 'pending',
        };

        $amount = (float) $order->total;
        $commission = round($amount * (self::DEFAULT_RATE / 100), 2);

        $earnedAt = $status === 'earned'
            ? (static::where('order_id', $order->id)->value('earned_at') ?? now())
            : null;

        return static::updateOrCreate(
            ['order_id' => $order->id],
            [
                'seller_id' => $order->seller_id,
                'order_amount' => $order->total,
                'commission_rate' => self::DEFAULT_RATE,
                'commission_amount' => $commission,
                'status' => $status,
                'earned_at' => $earnedAt,
            ]
        );
    }
}
