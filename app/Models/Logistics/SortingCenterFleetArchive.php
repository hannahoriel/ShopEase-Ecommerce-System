<?php

namespace App\Models\Logistics;

use Illuminate\Database\Eloquent\Model;

class SortingCenterFleetArchive extends Model
{
    protected $fillable = [
        'sorting_center_id', 'record_type', 'record_id', 'reason', 'snapshot', 'archived_by', 'archived_at',
    ];

    protected function casts(): array
    {
        return ['snapshot' => 'array', 'archived_at' => 'datetime'];
    }
}
