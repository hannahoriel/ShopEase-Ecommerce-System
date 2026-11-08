<?php

namespace App\Models\Logistics;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LogisticsMessage extends Model
{
    protected $fillable = [
        'conversation_id', 'sender_id', 'body', 'attachment_path', 'attachment_name',
        'attachment_mime_type', 'attachment_size', 'read_at',
    ];

    protected $casts = ['read_at' => 'datetime', 'attachment_size' => 'integer'];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(LogisticsConversation::class, 'conversation_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function reads(): HasMany
    {
        return $this->hasMany(LogisticsMessageRead::class, 'logistics_message_id');
    }
}
