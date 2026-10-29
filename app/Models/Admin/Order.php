<?php

namespace App\Models\Admin;

use App\Models\Rider\DeliveryProof;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (Order $order): void {
            $order->commission_amount = round(
                (float) $order->total * (CommissionTransaction::DEFAULT_RATE / 100),
                2
            );
        });

        static::saved(fn (Order $order) => CommissionTransaction::syncFromOrder($order));
    }

    protected $fillable = [
        'buyer_id',
        'seller_id',
        'order_number',
        'total',
        'commission_amount',
        'status',
        'pickup_date',
        'pickup_time',
        'delivery_name',
        'delivery_phone',
        'delivery_address',
        'payment_method',
        'shipping_fee',
        'voucher_discount',
        'voucher_code',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'voucher_discount' => 'decimal:2',
        'pickup_date' => 'date',
        'pickup_time' => 'datetime:H:i',
    ];

    protected $appends = [
        'pickup_date_display',
        'pickup_time_display',
        'cancellation_actor',
    ];

    public function getPickupDateDisplayAttribute(): ?string
    {
        return $this->pickup_date?->format('Y-m-d');
    }

    public function getPickupTimeDisplayAttribute(): ?string
    {
        return $this->pickup_time?->format('H:i');
    }

    public function getCancellationActorAttribute(): ?string
    {
        if ($this->status !== 'cancelled' || ! $this->relationLoaded('statusHistory')) {
            return null;
        }

        $cancellation = $this->statusHistory->firstWhere('to_status', 'cancelled');
        if (! $cancellation) {
            return null;
        }

        if ($this->relationLoaded('latestCancellationRequest')
            && $this->latestCancellationRequest?->status === 'approved') {
            return 'buyer';
        }

        return $cancellation->changedBy?->role;
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public function cancellationRequests(): HasMany
    {
        return $this->hasMany(OrderCancellationRequest::class);
    }

    public function latestCancellationRequest(): HasOne
    {
        return $this->hasOne(OrderCancellationRequest::class)->latestOfMany();
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class)->latest();
    }

    public function commissionTransaction(): HasOne
    {
        return $this->hasOne(CommissionTransaction::class);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    public function deliveryProofs(): HasMany
    {
        return $this->hasMany(DeliveryProof::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }
}
