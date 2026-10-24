<?php

namespace App\Models\Rider;

use App\Models\Admin\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodCollection extends Model
{
    protected $table = 'rider_cod_collections';

    protected $fillable = ['rider_id', 'order_id', 'amount', 'collected_at'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'collected_at' => 'datetime'];
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
