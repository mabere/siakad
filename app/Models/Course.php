<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'prerequisites' => 'array',
    ];

    public function getPrerequisitesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    protected static function booted()
    {
        static::created(function ($course) {
            $course->logActivity('created course');
        });

        static::updated(function ($course) {
            $course->logActivity('updated course');
        });

        static::deleted(function ($course) {
            $course->logActivity('deleted course');
        });
    }

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function lecturers(): BelongsToMany
    {
        return $this->belongsToMany(Lecturer::class, 'course_lecturer')
            ->withPivot('schedule_id', 'start_week', 'end_week')
            ->withTimestamps();
    }

    public function schedules()
    {
        return $this->morphMany(Schedule::class, 'schedulable');
    }


}