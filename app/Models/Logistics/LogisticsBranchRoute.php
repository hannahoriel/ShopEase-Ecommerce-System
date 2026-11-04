<?php

namespace App\Models\Logistics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogisticsBranchRoute extends Model
{
    protected $fillable = ['origin_branch_id', 'next_branch_id', 'status'];

    public function origin(): BelongsTo
    {
        return $this->belongsTo(LogisticsBranch::class, 'origin_branch_id');
    }

    public function next(): BelongsTo
    {
        return $this->belongsTo(LogisticsBranch::class, 'next_branch_id');
    }
}
