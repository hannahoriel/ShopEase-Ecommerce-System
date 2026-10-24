<?php

namespace App\Models\Rider;

use App\Models\Admin\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryAction extends Model
{
    protected $table = 'rider_delivery_actions';

    protected $fillable = [
        'rider_id',
        'order_id',
        'action',
        'reason',
        'notes',
        'reschedule_date',
        'reschedule_slot',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return ['reschedule_date' => 'date', 'reviewed_at' => 'datetime'];
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
