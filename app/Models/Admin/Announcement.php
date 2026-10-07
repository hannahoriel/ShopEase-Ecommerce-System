<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'body',
        'badge_label',
        'audience',
        'status',
        'published_at',
        'banner_path',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function scopePublishedForAudience(Builder $query, array $audiences): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $builder): void {
                $builder->whereNull('status')
                    ->orWhere('status', 'Published');
            })
            ->where(function (Builder $builder): void {
                $builder->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->whereIn('audience', $audiences)
            ->latest('published_at')
            ->latest('id');
    }
}
