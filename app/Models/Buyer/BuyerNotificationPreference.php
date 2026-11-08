<?php

namespace App\Models\Buyer;

use Illuminate\Database\Eloquent\Model;

class BuyerNotificationPreference extends Model
{
    protected $fillable = ['user_id', 'preferences'];

    protected $casts = ['preferences' => 'array'];
}
