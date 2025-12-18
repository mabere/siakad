<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bap extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendanceDetails()
    {
        return $this->hasManyThrough(AttendanceDetail::class, Attendance::class, 'bap_id', 'attendance_id');
    }

    public function getCourseAttribute()
    {
        return $this->schedule?->course ?? $this->mkduCourse;
    }

    public function schedulable(): MorphTo
    {
        return $this->morphTo();

    }

    public function mkduCourse()
    {
        return $this->belongsTo(MkduCourse::class, 'mkdu_course_id');
    }

}
