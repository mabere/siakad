<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MkduCourse extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'name', 'sks', 'semester_number', 'syllabus_path'];

    public function curriculum()
    {
        return $this->belongsToMany(Curriculum::class, 'curriculum_mkdu_course');
    }


    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
    public function studyPlans()
    {
        return $this->hasMany(StudyPlan::class, 'mkdu_course_id');
    }

    public function lecturers()
    {
        return $this->belongsToMany(Lecturer::class, 'lecturer_schedule', 'schedule_id', 'lecturer_id')
            ->withPivot('start_pertemuan', 'end_pertemuan')
            ->withTimestamps();
    }

    public function lecturer(): BelongsToMany
    {
        return $this->belongsToMany(Lecturer::class, 'mkdu_course_lecturer', 'mkdu_course_id', 'lecturer_id')
            ->withPivot('start_pertemuan', 'end_pertemuan')
            ->withTimestamps();
    }

    public function schedules()
    {
        return $this->morphMany(Schedule::class, 'schedulable');
    }

    public function curricula()
    {
        return $this->belongsToMany(Curriculum::class, 'curriculum_mkdu')
            ->withPivot('semester_number')
            ->withTimestamps();
    }

}
