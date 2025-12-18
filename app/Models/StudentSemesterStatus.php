<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSemesterStatus extends Model
{
    use HasFactory;
    protected $table = 'student_semester_status';

    protected $guarded = ['id'];
    // protected $fillable = ['student_id', 'academic_year_id', 'semester_id', 'status'];

    public function mahasiswa()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id', 'id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'id');
    }

}