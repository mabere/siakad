<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceDetail extends Model
{
    protected $fillable = [
        'attendance_id',
        'meeting_number',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }
}