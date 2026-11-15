<?php

namespace App\Models\Logistics;

use Illuminate\Database\Eloquent\Model;

class DeliveryHubParcelIssue extends Model
{
    protected $fillable = [
        'delivery_hub_id',
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
