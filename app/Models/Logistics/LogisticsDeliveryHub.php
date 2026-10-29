<?php

namespace App\Models\Logistics;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogisticsDeliveryHub extends Model
{
    protected $fillable = [
        'logistics_branch_id',
        'user_id',
        'hub_name',
        'province',
        'municipality',
        'barangay',
        'postal_code',
        'house_no',
        'street',
        'building',
        'subdivision',
        'manager_name',
        'manager_email',
        'manager_phone',
        'login_email',
        'status',
        'archived_at',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
    ];

    public function logisticsBranch(): BelongsTo
    {
        return $this->belongsTo(LogisticsBranch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
