<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\BelongsToManyRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Lecturer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'tgl' => 'date', // Cast sebagai objek Carbon
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function publications(): BelongsToMany
    {
        return $this->belongsToMany(Publication::class, 'lecturer_publication');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'lecturer_service');
    }

    public function questionnaires()
    {
        return $this->hasManyThrough(Questionnaire::class, Response::class, 'lecturer_id', 'id', 'id', 'questionnaire_id');
    }

    public function penunjang(): HasMany
    {
        return $this->hasMany(Addition::class);
    }

    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(Schedule::class, 'lecturer_schedule')
            ->withPivot('start_pertemuan', 'end_pertemuan');
    }

    public function mkduCourses()
    {
        return $this->belongsToMany(MkduCourse::class, 'mkdu_course_lecturer', 'lecturer_id', 'mkdu_course_id')
            ->withPivot('start_pertemuan', 'end_pertemuan')
            ->withTimestamps();
    }

    public function studentAdvisees(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_advisors')
            ->withPivot('advisor_type', 'assigned_at');
    }

    public function isFirstLecturer($schedule)
    {
        return $schedule->lecturersInSchedule()
            ->wherePivot('start_pertemuan', 1)
            ->where('lecturer_id', $this->id)
            ->exists();
    }

    public function getMeetingRange($schedule)
    {
        $pivot = $schedule->lecturersInSchedule()
            ->where('lecturer_id', $this->id)
            ->first()
            ->pivot;

        return [
            'start' => $pivot->start_pertemuan,
            'end' => $pivot->end_pertemuan
        ];
    }

    public function advisees(): HasMany
    {
        return $this->hasMany(Student::class, 'advisor_id');
    }

}
