<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThesisExamStatusLog extends Model
{
    protected $fillable = [
        'thesis_exam_id',
        'old_status',
        'new_status',
        'notes',
        'changed_by',
        'changed_at',
    ];

    public function thesisExam()
    {
        return $this->belongsTo(ThesisExam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}