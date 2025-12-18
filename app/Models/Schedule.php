<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Room;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedulable_id',
        'schedulable_type',
        'department_id',
        'academic_year_id',
        'room_id',
        'kelas_id',
        'start_time',
        'end_time',
        'hari',
    ];
    protected $with = ['course.department.faculty', 'room', 'kelas'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $query = self::where('kelas_id', $model->kelas_id)
                ->where('hari', $model->hari)
                ->where(function ($q) use ($model) {
                    $q->whereBetween('start_time', [$model->start_time, $model->end_time])
                        ->orWhereBetween('end_time', [$model->start_time, $model->end_time])
                        ->orWhere(function ($q) use ($model) {
                            $q->where('start_time', '<=', $model->start_time)
                                ->where('end_time', '>=', $model->end_time);
                        });
                });
            if ($query->exists()) {
                throw new \Exception("Jadwal duplikat ditemukan untuk kelas ini pada hari dan waktu yang sama.");
            }
        });
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'attendances', 'schedule_id', 'student_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function isValidForCurriculum()
    {
        $curriculum = $this->course->curriculum;
        return $curriculum && $curriculum->status === 'active' && $curriculum->department_id === $this->department_id;
    }

    public function currentMeeting()
    {
        $today = Carbon::now();
        $academicYearStart = Carbon::parse($this->academicYear->start_date);
        $weeksSinceStart = $today->diffInWeeks($academicYearStart);
        $currentMeeting = $weeksSinceStart + 1;
        return min(max($currentMeeting, 1), 16);
    }

    public function grade(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'schedule_id');
    }

    public function scopeForLecturer($query, $lecturerId)
    {
        return $query->whereHas('lecturersInSchedule', function ($q) use ($lecturerId) {
            $q->where('lecturer_id', $lecturerId);
        });
    }

    public function getIsMkduAttribute()
    {
        return $this->mkdu_course_id !== null;
    }

    /* EDOM & Course Evaluation*/
    public function monitorings(): HasMany
    {
        return $this->hasMany(LearningMonitoring::class);
    }
    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }
    public function getHasFilledEdomAttribute(): bool
    {
        $academicYear = AcademicYear::where('status', 1)->first();

        if (!$academicYear || !auth()->id()) {
            return false;
        }

        // Get student_id from authenticated user
        $student = Student::where('user_id', auth()->id())->first();

        if (!$student) {
            return false;
        }

        return $this->responses()
            ->where('academic_year_id', $academicYear->id)
            ->where('student_id', $student->id)
            ->exists();
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when(
            $filters['search'] ?? false,
            fn($query, $search) =>
            $query->whereHas(
                'course',
                fn($q) =>
                $q->where('name', 'like', "%$search%")
            )
        );

        $query->when(
            $filters['year'] ?? false,
            fn($query, $year) =>
            $query->whereHas(
                'academicYear',
                fn($q) =>
                $q->whereYear('start_date', $year)
            )
        );

        $query->when(
            $filters['semester'] ?? false,
            fn($query, $semester) =>
            $query->whereHas(
                'academicYear',
                fn($q) =>
                $q->where('semester', $semester)
            )
        );
    }
    /* END EDOM & Course Evaluation*/

    public function lecturersInSchedule(): BelongsToMany
    {
        return $this->belongsToMany(Lecturer::class, 'lecturer_schedule', 'schedule_id', 'lecturer_id')
            ->withPivot('start_pertemuan', 'end_pertemuan')
            ->withTimestamps();
    }

    public function lecturers()
    {
        return $this->belongsToMany(Lecturer::class, 'lecturer_schedule')
            ->withPivot('start_pertemuan', 'end_pertemuan')
            ->withTimestamps();
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function studyPlans(): HasMany
    {
        return $this->hasMany(StudyPlan::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'schedule_id', 'id');
    }

    public function mkduCourse()
    {
        return $this->belongsTo(MkduCourse::class);
    }

    public function schedulable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getCourseAttribute()
    {
        return $this->schedulable;
    }

    // Contoh accessor untuk mendapatkan nama mata kuliah
    public function getCourseNameAttribute()
    {
        return $this->schedulable ? $this->schedulable->name : 'N/A';
    }

    // Contoh accessor untuk mendapatkan kode mata kuliah
    public function getCourseCodeAttribute()
    {
        return $this->schedulable ? $this->schedulable->code : 'N/A';
    }

    // Contoh accessor untuk mendapatkan jenis mata kuliah (Prodi/MKDU)
    public function getCourseTypeAttribute()
    {
        return $this->schedulable_type === 'App\\Models\\MkduCourse' ? 'MKDU' : 'Prodi';
    }

    public function getSksValueAttribute()
    {
        return $this->schedulable ? $this->schedulable->sks : 0;
    }

}