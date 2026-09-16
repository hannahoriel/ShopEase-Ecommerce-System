<?php

namespace App\Models;

use App\Models\Buyer\Buyer;
use App\Models\Logistics\Logistics;
use App\Models\Rider\Rider;
use App\Models\Seller\Seller;
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
        'email',
        'password',
        'role',
        'email_verified_at',
        'registration_status',
        'approved_at',
        'rejected_at',
        'suspended_until',
        'account_action_reason',
        'account_action_details',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
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
