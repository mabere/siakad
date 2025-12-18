<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Curriculum extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = ['department_id', 'academic_year_id', 'name', 'description', 'status'];


    protected $casts = [
        'status' => 'string',
    ];


    protected static function booted()
    {
        static::created(function ($curriculum) {
            $curriculum->logActivity('created curriculum');
        });

        static::updated(function ($curriculum) {
            $curriculum->logActivity('updated curriculum');
        });

        static::deleted(function ($curriculum) {
            $curriculum->logActivity('deleted curriculum');
        });
    }

    use SoftDeletes;

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function evaluations()
    {
        return $this->hasMany(CurriculumEvaluation::class);
    }

    public function mkduCourses()
    {
        return $this->belongsToMany(MkduCourse::class, 'curriculum_mkdu')
            ->withPivot('semester_number')
            ->withTimestamps();
    }




}
