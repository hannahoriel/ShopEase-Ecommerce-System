<?php

namespace App\Models\Logistics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SortingCenterVehicle extends Model
{
    protected $fillable = [
        'sorting_center_id', 'name', 'type', 'plate_number', 'make', 'model', 'year', 'capacity',
        'driver_name', 'registration_date', 'availability', 'color', 'or_cr_path', 'notes',
    ];

    protected function casts(): array
    {
        return ['registration_date' => 'datetime', 'year' => 'integer'];
    }

    protected $appends = ['or_cr_url'];

    public function getOrCrUrlAttribute(): ?string
    {
        return $this->or_cr_path ? Storage::disk('public')->url($this->or_cr_path) : null;
    }
}
