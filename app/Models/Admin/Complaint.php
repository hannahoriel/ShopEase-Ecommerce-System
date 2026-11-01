<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'subject',
        'type',
        'description',
        'status',
        'evidence',
    ];

    protected $casts = [
        'evidence' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function updates(): HasMany
    {
        return $this->hasMany(ComplaintUpdate::class)->latest();
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(ComplaintUpdate::class)->latest();
    }
}
