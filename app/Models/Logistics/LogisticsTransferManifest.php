<?php

namespace App\Models\Logistics;

use App\Models\Admin\Shipment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LogisticsTransferManifest extends Model
{
    protected $fillable = [
        'batch_key', 'origin_type', 'origin_id', 'destination_type', 'destination_id',
        'courier_registration_id', 'vehicle_id', 'courier_name', 'vehicle_reference',
        'returning_manifest_id', 'status', 'scheduled_at', 'loaded_at', 'departed_at',
        'arrival_confirmed_at', 'arrived_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'loaded_at' => 'datetime',
        'departed_at' => 'datetime',
        'arrival_confirmed_at' => 'datetime',
        'arrived_at' => 'datetime',
    ];

    public function shipments(): BelongsToMany
    {
        return $this->belongsToMany(Shipment::class, 'logistics_transfer_manifest_shipments', 'transfer_manifest_id', 'shipment_id')
            ->withPivot([
                'received_at',
                'received_by',
                'load_status',
                'loaded_at',
                'loaded_by',
                'discrepancy_note',
            ])->withTimestamps();
    }

    public function returnTrip(): BelongsTo
    {
        return $this->belongsTo(self::class, 'returning_manifest_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(LogisticsTransferManifestEvent::class, 'transfer_manifest_id')
            ->orderBy('occurred_at');
    }
}
