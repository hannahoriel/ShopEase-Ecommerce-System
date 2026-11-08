<?php

namespace App\Models\Rider;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Remittance extends Model
{
    protected $table = 'rider_remittances';

    protected $fillable = [
        'rider_id',
        'delivery_hub_id',
        'amount',
        'actual_amount',
        'discrepancy_amount',
        'method',
        'reference_number',
        'receipt_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'cash_in_at',
        'cash_in_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2', 'actual_amount' => 'decimal:2', 'discrepancy_amount' => 'decimal:2',
            'reviewed_at' => 'datetime', 'cash_in_at' => 'datetime',
        ];
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function deliveryHub(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Logistics\LogisticsDeliveryHub::class, 'delivery_hub_id');
    }

    public function collections(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(CodCollection::class, 'rider_cod_remittance_items', 'remittance_id', 'cod_collection_id')
            ->withTimestamps();
    }
}
