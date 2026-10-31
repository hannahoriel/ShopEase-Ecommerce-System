<?php

namespace App\Models\Logistics;

use App\Models\Admin\Shipment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LogisticsTransferManifest extends Model
{
    protected $fillable = [
        'batch_key', 'origin_type', 'origin_id', 'destination_type', 'destination_id',
        'courier_registration_id', 'vehicle_id', 'courier_name', 'vehicle_reference',
        'returning_manifest_id', 'status', 'loaded_at', 'arrived_at',
    ];

    protected $casts = ['loaded_at' => 'datetime', 'arrived_at' => 'datetime'];

    public function shipments(): BelongsToMany
    {
        return $this->belongsToMany(Shipment::class, 'logistics_transfer_manifest_shipments', 'transfer_manifest_id', 'shipment_id')
            ->withPivot(['received_at'])->withTimestamps();
    }

    public function returnTrip(): BelongsTo
    {
        return $this->belongsTo(self::class, 'returning_manifest_id');
    }
}
