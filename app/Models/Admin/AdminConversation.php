<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminConversation extends Model
{
    public const CATEGORY_LOGISTICS = 'logistics';

    public const CATEGORY_BUYERS = 'buyers';

    public const CATEGORY_SELLERS = 'sellers';

    public const CATEGORY_COMPLAINTS = 'complaints';

    public const CATEGORIES = [
        self::CATEGORY_LOGISTICS,
        self::CATEGORY_BUYERS,
        self::CATEGORY_SELLERS,
        self::CATEGORY_COMPLAINTS,
    ];

    protected $fillable = [
        'category',
        'user_id',
        'complaint_id',
        'complaint_party',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AdminMessage::class, 'conversation_id')
            ->orderBy('created_at')
            ->orderBy('id');
    }
}
