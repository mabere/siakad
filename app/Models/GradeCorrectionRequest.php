<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeCorrectionRequest extends Model
{
    use HasFactory;
    protected $table = 'grade_correction_requests';
    protected $guarded = ['id'];
    protected $casts = [
        'approval_flow' => 'array',
        'approval_history' => 'array',
        'form_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getCourseAttribute()
{
    return $this->schedule?->course ?? $this->mkduCourse;
}

}
