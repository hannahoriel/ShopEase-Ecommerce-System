<?php

namespace App\Models\Admin;

use App\Models\Rider\Rider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerPickupAttempt extends Model
{
    protected $fillable = [
        'order_id',
        'rider_id',
        'attempt_number',
        'pickup_date',
        'pickup_time',
        'status',
        'missed_at',
        'picked_up_at',
    ];

    protected function casts(): array
    {
        return [
            'pickup_date' => 'date',
            'pickup_time' => 'datetime:H:i',
            'missed_at' => 'datetime',
            'picked_up_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }
}
