<?php

namespace App\Models\Logistics;

use Illuminate\Database\Eloquent\Model;

class DeliveryHubFleetArchive extends Model
{
    protected $fillable = [
        'logistics_delivery_hub_id', 'record_type', 'record_id', 'reason', 'snapshot', 'archived_by', 'archived_at',
    ];

    protected function casts(): array
    {
        return ['snapshot' => 'array', 'archived_at' => 'datetime'];
    }
}
