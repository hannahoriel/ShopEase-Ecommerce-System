<?php

namespace App\Models\Logistics;

use App\Models\Rider\Rider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LogisticsBranch extends Model
{
    protected $fillable = [
        'logistics_id',
        'name',
        'address',
        'contact_person',
        'phone',
    ];

    public function logistics(): BelongsTo
    {
        return $this->belongsTo(Logistics::class);
    }

    public function riders(): HasMany
    {
        return $this->hasMany(Rider::class);
    }
}
