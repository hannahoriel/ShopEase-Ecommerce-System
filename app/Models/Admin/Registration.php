<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_type',
        'last_name',
        'first_name',
        'middle_name',
        'sex',
        'birthdate',
        'email',
        'phone',
        'password',
        'province',
        'municipality',
        'barangay',
        'street',
        'house_no',
        'zip_code',
        'business_name',
        'business_category',
        'business_permit_path',
        'valid_id_path',
        'status',
        'rejection_reason',
        'rejection_details',
        'reviewed_by',
        'reviewed_at',
        'user_id',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'reviewed_at' => 'datetime',
    ];

    protected $appends = [
        'full_name',
    ];

    /**
     * Accessor: "First Middle Last" for display, emails, and archive rows.
     */
    public function getFullNameAttribute(): string
    {
        return trim(preg_replace('/\s+/', ' ', "{$this->first_name} {$this->middle_name} {$this->last_name}"));
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }
}
