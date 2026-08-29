<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
            'password' => 'hashed',
        ];
    }
}
