<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LecturerSchedule extends Model
{
    use HasFactory;

    protected $table = 'lecturer_schedule';

    protected $guarded = ['id'];

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function mkduCourse(): BelongsTo
    {
        return $this->belongsTo(MkduCourse::class, 'mkdu_course_id');
    }

}