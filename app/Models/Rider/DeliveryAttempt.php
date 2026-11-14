<?php

namespace App\Models\Rider;

use App\Models\Admin\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryAttempt extends Model
{
    protected $table = 'rider_delivery_attempts';

    protected $fillable = ['order_id', 'rider_id', 'attempt_number', 'reason', 'notes', 'evidence_path'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }
}
