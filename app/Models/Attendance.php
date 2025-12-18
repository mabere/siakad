<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;
    const ABSENT = 'Tanpa Keterangan';
    const PERMIT = 'Izin';
    const PRESENT = 'Hadir';

    protected $guarded = ['id'];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'id');
    }

    public function bap(): BelongsTo
    {
        return $this->belongsTo(Bap::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function studyPlan(): BelongsTo
    {
        return $this->belongsTo(StudyPlan::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class, 'attendance_id');
    }
    public function attendanceDetails(): HasMany
    {
        return $this->hasMany(AttendanceDetail::class, 'attendance_id');
    }

    public function getTotalHadirAttribute()
    {
        return $this->attendanceDetails()->where('status', self::PRESENT)->count();
    }

    public function getTotalIzinAttribute()
    {
        return $this->attendanceDetails()->where('status', self::PERMIT)->count();
    }

    public function getTotalAlfaAttribute()
    {
        return $this->attendanceDetails()->where('status', self::ABSENT)->count();
    }

    public function getPersentaseAttribute()
    {
        $totalPertemuan = 16; // bisa juga $this->attendanceDetails->count() kalau ingin dinamis
        return $totalPertemuan > 0 ? round(($this->total_hadir / $totalPertemuan) * 100, 2) : 0;
    }

    public function getCourseAttribute()
    {
        return $this->schedule?->course ?? $this->mkduCourse;
    }

}