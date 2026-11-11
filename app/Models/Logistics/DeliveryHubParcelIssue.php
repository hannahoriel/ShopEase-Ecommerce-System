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
    ];

    protected function casts(): array
    {
        return ['accountability' => 'array'];
    }
}
