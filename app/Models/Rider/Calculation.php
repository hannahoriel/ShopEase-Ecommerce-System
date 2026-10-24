<?php

namespace App\Models\Rider;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calculation extends Model
{
    protected $table = 'rider_calculations';

    protected $fillable = [
        'rider_id',
        'expression',
        'result',
        'category',
    ];

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }
}
