<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id');
    }

    public function lecturers(): HasMany
    {
        return $this->hasMany(Lecturer::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function kaprodiLecturer()
    {
        return $this->belongsTo(Lecturer::class, 'head_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function schedules()
    {
        return $this->hasManyThrough(Schedule::class, Course::class, 'department_id', 'course_id');
    }
}
