<?php

namespace App\Models\Rider;

use App\Models\Admin\Order;
use App\Models\Admin\OrderReturn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodCollection extends Model
{
    protected $table = 'rider_cod_collections';

    protected $fillable = ['rider_id', 'order_id', 'order_return_id', 'amount', 'collected_at'];

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

    public function orderReturn(): BelongsTo
    {
        return $this->belongsTo(OrderReturn::class, 'order_return_id');
    }

    public function remittances(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Remittance::class, 'rider_cod_remittance_items', 'cod_collection_id', 'remittance_id')
            ->withTimestamps();
    }
}
