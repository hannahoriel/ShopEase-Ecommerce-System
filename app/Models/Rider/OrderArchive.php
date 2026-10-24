<?php

namespace App\Models\Rider;

use App\Models\Admin\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderArchive extends Model
{
    protected $table = 'rider_order_archives';

    protected $fillable = [
        'rider_id',
        'order_id',
        'archived_at',
        'dismissed_at',
    ];

    protected function casts(): array
    {
        return [
            'archived_at' => 'datetime',
            'dismissed_at' => 'datetime',
        ];
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
