<?php

namespace App\Models\Logistics;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LogisticsConversation extends Model
{
    protected $fillable = [
        'logistics_id', 'contact_type', 'contact_user_id', 'logistics_branch_id', 'logistics_delivery_hub_id',
    ];

    public function logistics(): BelongsTo
    {
        return $this->belongsTo(Logistics::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contact_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(LogisticsMessage::class, 'conversation_id')->orderBy('created_at')->orderBy('id');
    }
}
