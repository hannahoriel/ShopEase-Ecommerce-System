<?php

namespace App\Models\Logistics;

use App\Models\Rider\Rider;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LogisticsBranch extends Model
{
    protected $fillable = [
        'logistics_id',
        'user_id',
        'name',
        'address',
        'contact_person',
        'phone',
        'province',
        'municipality',
        'barangay',
        'postal_code',
        'house_no',
        'street',
        'building',
        'subdivision',
        'manager_email',
        'login_email',
        'status',
        'archived_at',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
    ];

    public function logistics(): BelongsTo
    {
        return $this->belongsTo(Logistics::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function riders(): HasMany
    {
        return $this->hasMany(Rider::class);
    }

    public function deliveryHubs(): HasMany
    {
        return $this->hasMany(LogisticsDeliveryHub::class);
    }
}
