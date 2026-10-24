<?php

namespace App\Models\Rider;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    protected $table = 'rider_attendance_records';

    protected $fillable = [
        'rider_id',
        'attendance_date',
        'clock_in_at',
        'clock_in_photo',
        'clock_out_at',
        'clock_out_photo',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'clock_in_at' => 'datetime',
            'clock_out_at' => 'datetime',
        ];
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }
}
