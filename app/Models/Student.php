<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'tgl' => 'date', // Cast sebagai objek Carbon
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function thesisSupervision(): HasOne
    {
        return $this->hasOne(ThesisSupervision::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function tuitionFee(): HasOne
    {
        return $this->hasOne(TuitionFee::class);
    }

    public function krs(): HasMany
    {
        return $this->hasMany(StudyPlan::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'student_id', 'id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function tuitionfees(): HasMany
    {
        return $this->hasMany(Tuitionfee::class);
    }

    public function isActive()
    {
        $latestPayment = $this->tuitionfees()->latest()->first();
        $currentSemester = AcademicYear::where('is_active', true)->first();

        return $latestPayment && $latestPayment->status === 'LUNAS' &&
            $latestPayment->payment_date >= $currentSemester->registration_start_date &&
            $latestPayment->payment_date <= $currentSemester->registration_end_date;
    }

    public function advisors()
    {
        return $this->belongsToMany(Lecturer::class, 'student_advisors')
            ->withPivot('advisor_type', 'assigned_at', 'completed_at');
    }

    public function advisor()
    {
        return $this->belongsTo(Lecturer::class, 'advisor_id');
    }

    public function primaryAdvisors(): BelongsToMany
    {
        return $this->advisors()->where('advisor_type', 'primary');
    }

    public function secondaryAdvisors(): BelongsToMany
    {
        return $this->advisors()->where('advisor_type', 'secondary');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function thesis()
    {
        return $this->hasOne(Thesis::class);
    }

    public function getCurrentSemester($currentAcademicYearTa, $currentTerm)
    {
        $entryYear = $this->entry_year ?? 2022;
        $entrySemester = $this->entry_semester ?? 1;
        $currentYear = (int) substr($currentAcademicYearTa, 0, 4);
        $currentSemester = $currentTerm === 'Ganjil' ? 1 : 2;
        $semestersPassed = (($currentYear - $entryYear) * 2) + $currentSemester;
        $currentLevelSemester = $semestersPassed - ($entrySemester - 1);

        return max(1, $currentLevelSemester);
    }

    public function alumni()
    {
        return $this->hasOne(Alumni::class, 'student_id', 'id');
    }

    public function studentSemesterStatus()
    {
        return $this->hasOne(StudentSemesterStatus::class)->latest('effective_date');
    }

    public function statusSemesters()
    {
        return $this->hasMany(StudentSemesterStatus::class, 'student_id');
    }
}