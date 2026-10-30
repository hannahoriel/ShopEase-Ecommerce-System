<?php

namespace App\Models\Logistics;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryHubRiderRegistration extends Model
{
    protected $fillable = [
        'logistics_delivery_hub_id',
        'user_id',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function deliveryHub(): BelongsTo
    {
        return $this->belongsTo(LogisticsDeliveryHub::class, 'logistics_delivery_hub_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
