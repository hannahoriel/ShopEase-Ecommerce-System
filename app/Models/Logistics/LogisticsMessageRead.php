<?php

namespace App\Models\Logistics;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogisticsMessageRead extends Model
{
    public $timestamps = false;

    protected $fillable = ['logistics_message_id', 'user_id', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    public function message(): BelongsTo
    {
        return $this->belongsTo(LogisticsMessage::class, 'logistics_message_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
