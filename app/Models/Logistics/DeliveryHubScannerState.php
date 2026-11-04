<?php

namespace App\Models\Logistics;

use Illuminate\Database\Eloquent\Model;

class DeliveryHubScannerState extends Model
{
    protected $fillable = ['logistics_delivery_hub_id', 'updated_by', 'state'];

    protected function casts(): array
    {
        return ['state' => 'array'];
    }
}
