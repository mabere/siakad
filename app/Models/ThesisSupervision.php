<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThesisSupervision extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'thesis_id',
        'supervisor_id',
        'supervisor_role',
        'status',
        'assigned_at'
    ];

    protected $casts = [
        'assigned_at' => 'datetime'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(Lecturer::class, 'supervisor_id');
    }

    public function thesis()
    {
        return $this->belongsTo(Thesis::class);
    }

    // Accessor methods for primary and secondary supervisors
    public function getPrimarySupervisorAttribute()
    {
        if ($this->supervisor_role === 'pembimbing_1') {
            return $this->supervisor;
        }
        return ThesisSupervision::where('thesis_id', $this->thesis_id)
            ->where('supervisor_role', 'pembimbing_1')
            ->first()
                ?->supervisor;
    }

    public function getSecondarySupervisorAttribute()
    {
        if ($this->supervisor_role === 'pembimbing_2') {
            return $this->supervisor;
        }
        return ThesisSupervision::where('thesis_id', $this->thesis_id)
            ->where('supervisor_role', 'pembimbing_2')
            ->first()
                ?->supervisor;
    }
}
