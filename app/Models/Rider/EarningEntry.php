<?php

namespace App\Models\Rider;

use App\Models\Admin\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EarningEntry extends Model
{
    protected $table = 'rider_earning_entries';

    protected $fillable = [
        'rider_id',
        'order_id',
        'posted_by',
        'type',
        'description',
        'amount',
        'status',
        'posted_at',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'posted_at' => 'datetime'];
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
