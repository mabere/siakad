<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Question;
use App\Models\Student;
use App\Models\Questionnaire;
use App\Models\AcademicYear;

class Response extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'rating' => 'integer',
        'student_id' => 'integer',
        'questionnaire_id' => 'integer',
        'question_id' => 'integer'
    ];

    // protected $fillable = [
    //     'schedule_id',
    //     'questionnaire_id',
    //     'academic_year_id',
    //     'student_id',
    //     'question_id',
    //     'rating'
    // ];


    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
