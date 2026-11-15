<?php

namespace App\Models\Logistics;

use Illuminate\Database\Eloquent\Model;

class SortingCenterParcelIssue extends Model
{
    protected $fillable = [
        'sorting_center_id',
        'reported_by',
        'tracking_number',
        'issue_type',
        'description',
        'evidence_name',
        'evidence_path',
        'status',
        'accountability',
        'admin_decision',
        'decision_notes',
        'decided_by',
        'decided_at',
    ];

    protected function casts(): array
    {
        return ['accountability' => 'array', 'decided_at' => 'datetime'];
    }
}
