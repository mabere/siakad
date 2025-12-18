<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudyPlan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $hidden = [];

    protected $casts = [
        'approved_at' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'approved_by');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function scopeApprovedForSchedule($query, $scheduleId, $academicYearId)
    {
        return $query->where('schedule_id', $scheduleId)
            ->where('academic_year_id', $academicYearId)
            ->where('status', 'approved');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function mkduCourse()
    {
        return $this->belongsTo(MkduCourse::class, 'mkdu_course_id');
    }

    public function getCourseAttribute()
    {
        return $this->schedule?->course ?? $this->mkduCourse;
    }

    public function getLecturersAttribute()
    {
        return $this->schedule?->lecturersInSchedule ?? collect();
    }

    public function getHariAttribute()
    {
        return $this->schedule?->hari;
    }

    public function getWaktuAttribute()
    {
        return $this->schedule
            ? $this->schedule->start_time . ' - ' . $this->schedule->end_time
            : null;
    }
    public function getFirstLecturerNameAttribute()
    {
        if ($this->schedule && $this->schedule->lecturersInSchedule->isNotEmpty()) {
            return $this->schedule->lecturersInSchedule->first()->nama_dosen;
        }

        if ($this->mkduCourse && $this->mkduCourse->lecturers->isNotEmpty()) {
            return $this->mkduCourse->lecturers->first()->nama_dosen;
        }

        return '-';
    }

    public function schedulable(): MorphTo
    {
        return $this->morphTo();

    }

}