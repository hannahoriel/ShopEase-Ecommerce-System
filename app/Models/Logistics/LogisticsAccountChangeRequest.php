<?php

namespace App\Models\Logistics;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogisticsAccountChangeRequest extends Model
{
    protected $fillable = ['user_id', 'section', 'requested_data', 'status', 'reviewed_by', 'reviewed_at', 'review_note'];

    protected $casts = ['requested_data' => 'array', 'reviewed_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
