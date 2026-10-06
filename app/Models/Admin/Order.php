<?php

namespace App\Models\Admin;

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
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'pickup_date' => 'date',
        'pickup_time' => 'datetime:H:i',
    ];

    protected $appends = [
        'pickup_date_display',
        'pickup_time_display',
    ];

    public function getPickupDateDisplayAttribute(): ?string
    {
        return $this->pickup_date?->format('Y-m-d');
    }

    public function getPickupTimeDisplayAttribute(): ?string
    {
        return $this->pickup_time?->format('H:i');
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

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
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
