<?php

namespace App\Models\Rider;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recipient extends Model
{
    protected $table = 'rider_recipients';

    protected $fillable = ['rider_id', 'name', 'phone', 'address', 'note'];

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }
}
