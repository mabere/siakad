<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MkduCourseLecturer extends Model
{
    use HasFactory;

    protected $table = 'mkdu_course_lecturer';

    protected $fillable = [
        'mkdu_course_id',
        'lecturer_id',
        'start_pertemuan',
        'end_pertemuan',
    ];

    public function mkduCourse(): BelongsTo
    {
        return $this->belongsTo(MkduCourse::class, 'mkdu_course_id');
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id');
    }
}
