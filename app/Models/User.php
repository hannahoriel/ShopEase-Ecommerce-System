<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_BUYER = 'buyer';
    const ROLE_SELLER = 'seller';
    const ROLE_RIDER = 'rider';
    const ROLE_ADMIN = 'admin';
    const ROLE_LOGISTICS = 'logistics';

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'middle_initial',
        'sex',
        'email',
        'contact_no',
        'birthday',
        'age',
        'province',
        'municipality',
        'barangay',
        'street',
        'house_number',
        'business_name',
        'line_of_business',
        'vehicle',
        'plate_number',
        'upload_id',
        'upload_business_permit',
        'upload_or_cr',
        'upload_id_license',
        'password',
        'role',
        'registration_status',
        'approved_at',
        'rejected_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birthday' => 'date',
            'age' => 'integer',
            'approved_at' => 'datetime',
            'suspended_until' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function buyerProfile()
    {
        return $this->hasOne(Buyer::class);
    }

    public function sellerProfile()
    {
        return $this->hasOne(Seller::class);
    }

    public function riderProfile()
    {
        return $this->hasOne(Rider::class);
    }

    public function logisticsProfile()
    {
        return $this->hasOne(Logistics::class);
    }

    public function scopePending($query)
    {
        return $query->where('registration_status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('registration_status', 'active');
    }
}
