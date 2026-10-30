<?php

namespace App\Models\Logistics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogisticsAccountSetting extends Model
{
    protected $fillable = ['user_id', 'privacy_settings', 'notification_preferences', 'cover_photo_path'];

    protected $casts = ['privacy_settings' => 'array', 'notification_preferences' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
