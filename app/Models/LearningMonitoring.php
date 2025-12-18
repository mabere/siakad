<?php

namespace App\Models;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LearningMonitoring extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'academic_year_id',
        'department_id',
        'monitor_id',
        'meeting_number',
        'monitoring_date',
        'start_time',
        'end_time',
        'attendance_count',
        'material_conformity',
        'learning_method',
        'media_used',
        'notes',
        'status',
        'verification_notes',    // Tambahkan ini
        'revision_notes',        // Tambahkan ini
        'verified_at',           // Tambahkan ini
        'verified_by'            // Tambahkan ini
    ];

    // Accessor untuk status color
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'draft' => 'warning',
            'submitted' => 'info',
            'verified' => 'success',
            'revised' => 'primary',
            'completed' => 'success',
            default => 'secondary'
        };
    }

    // Accessor untuk status label
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Diajukan',
            'verified' => 'Terverifikasi',
            'revised' => 'Perlu Revisi',
            'completed' => 'Selesai',
            default => 'Unknown'
        };
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function aspects()
    {
        return $this->hasMany(MonitoringAspect::class, 'monitoring_id');
    }

    public function monitor()
    {
        return $this->belongsTo(User::class, 'monitor_id');
    }

    // Tambahkan relasi ke academic year
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}