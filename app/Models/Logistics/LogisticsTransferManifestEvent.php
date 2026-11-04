<?php

namespace App\Models\Logistics;

use App\Models\Admin\Shipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogisticsTransferManifestEvent extends Model
{
    protected $fillable = [
        'transfer_manifest_id',
        'shipment_id',
        'actor_user_id',
        'event_type',
        'notes',
        'metadata',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    public function manifest(): BelongsTo
    {
        return $this->belongsTo(LogisticsTransferManifest::class, 'transfer_manifest_id');
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
