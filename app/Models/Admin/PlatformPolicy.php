<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PlatformPolicy extends Model
{
    protected $fillable = [
        'title',
        'category',
        'description',
        'content',
        'status',
        'published_at',
        'created_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
