<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThesisExamScore extends Model
{
    protected $fillable = [
        'thesis_exam_id',
        'lecturer_id',
        'criteria_id',
        'score',
        'notes'
    ];

    protected $casts = [
        'score' => 'double',
    ];

    public function thesisExam()
    {
        return $this->belongsTo(ThesisExam::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }


    public function criteria()
    {
        return $this->belongsTo(ThesisExamCriterium::class, 'criteria_id');
    }
}
