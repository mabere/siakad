<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;

class Alumni extends Model
{
    protected $table = 'alumni';

    protected $fillable = [
        'student_id',
        'graduation_year',
        'job_title',
        'company',
        'industry',
        'salary_range',
        'further_education',
        'contribution',
        'status',
        'visibility',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}