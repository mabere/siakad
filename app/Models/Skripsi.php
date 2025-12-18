<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skripsi extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'title',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function documents()
    {
        return $this->hasMany(ThesisDocument::class);
    }
    public function exam()
    {
        return $this->hasOne(ThesisExam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function supervisions()
    {
        return $this->hasMany(ThesisSupervision::class);
    }

    public function meetings()
    {
        return $this->hasMany(SupervisionMeeting::class);
    }

    public function milestones()
    {
        return $this->hasMany(ThesisMilestone::class);
    }

    public function allDocumentsApproved(): bool
    {
        return $this->documents()->where('status', '!=', 'approved')->count() === 0;
    }

    public function examiners(): HasMany
    {
        return $this->hasMany(ThesisExamExaminer::class);
    }
}