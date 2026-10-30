<?php

namespace App\Models\Logistics;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierRegistration extends Model
{
    protected $fillable = [
        'sorting_center_id', 'user_id', 'first_name', 'middle_name', 'last_name', 'sex', 'birthday',
        'email', 'phone', 'region', 'province', 'city', 'barangay', 'street', 'house_no',
        'postal_code', 'vehicle', 'plate_number', 'or_cr_path', 'drivers_license_path',
        'status', 'rejection_reason', 'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return ['birthday' => 'date', 'reviewed_at' => 'datetime'];
    }

    protected $appends = ['or_cr_url', 'drivers_license_url'];

    public function getOrCrUrlAttribute(): ?string
    {
        return $this->or_cr_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->or_cr_path) : null;
    }

    public function getDriversLicenseUrlAttribute(): ?string
    {
        return $this->drivers_license_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->drivers_license_path) : null;
    }

    public function sortingCenter(): BelongsTo
    {
        return $this->belongsTo(LogisticsBranch::class, 'sorting_center_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
