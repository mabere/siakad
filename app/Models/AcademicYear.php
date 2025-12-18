<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function tuitionFees(): HasMany
    {
        return $this->hasMany(Tuitionfee::class);
    }

    public static function active()
    {
        return self::where('status', 1)->first();
    }

    public function statusSemesters()
    {
        return $this->hasMany(StudentSemesterStatus::class, 'academic_year_id');
    }

    public function getSmtAttribute()
    {
        $semester = strtolower($this->attributes['semester']);
        if ($semester == 'ganjil') {
            return 1;
        } elseif ($semester == 'genap') {
            return 2;
        }
        return null;
    }

    public function isKrsPeriodActive()
    {
        $now = now();
        return $now->between($this->krs_open_date, $this->krs_close_date);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'academic_year_id');
    }

    public function curricula()
    {
        return $this->hasMany(Curriculum::class);
    }

}
