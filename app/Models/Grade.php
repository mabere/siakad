<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grade extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_validated' => 'boolean',
        'validation_status' => 'string',
        'validation_deadline' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function studyPlan(): BelongsTo
    {
        return $this->belongsTo(StudyPlan::class);
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class, 'student_id', 'student_id')
            ->where('schedule_id', $this->schedule_id);
    }

    public function getAttendanceDetails()
    {
        return AttendanceDetail::join('attendances', 'attendance_details.attendance_id', '=', 'attendances.id')
            ->where('attendances.student_id', $this->student_id)
            ->where('attendances.schedule_id', $this->schedule_id)
            ->where('attendances.academic_year_id', $this->academic_year_id)
            ->get();
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public static function calculateIpk($studentId, $academicYearId = null)
    {
        $grades = self::where('student_id', $studentId)
            ->whereNotNull('nhuruf')
            ->with(['schedule.course:id,sks'])
            ->get();

        $totalSks = 0;
        $totalBobot = 0;

        foreach ($grades as $grade) {
            $sks = $grade->schedule && $grade->schedule->course ? $grade->schedule->course->sks : 0;
            $bobot = self::convertNhurufToAngka($grade->nhuruf) * $sks;
            $totalSks += $sks;
            $totalBobot += $bobot;
        }

        $ipk = $totalSks > 0 ? number_format($totalBobot / $totalSks, 2) : 0;
        return $ipk;
    }

    public static function getIpkHistory($studentId)
    {
        $grades = self::where('student_id', $studentId)
            ->whereNotNull('nhuruf')
            ->with(['schedule.course:id,sks', 'academicYear'])
            ->get()
            ->groupBy('academic_year_id');

        return $grades->map(function ($yearGrades) {
            $totalSks = 0;
            $totalBobot = 0;

            foreach ($yearGrades as $grade) {
                $sks = $grade->schedule && $grade->schedule->course ? $grade->schedule->course->sks : 0;
                $totalSks += $sks;
                $totalBobot += self::convertNhurufToAngka($grade->nhuruf) * $sks;
            }

            $ipk = $totalSks > 0 ? number_format($totalBobot / $totalSks, 2) : 0;
            $year = $yearGrades->first()->academicYear ? $yearGrades->first()->academicYear->ta . '/' . $yearGrades->first()->academicYear->semester : 'Unknown';

            return [
                'year' => $year,
                'ipk' => $ipk,
            ];
        });
    }

    public static function convertNhurufToAngka($nhuruf)
    {
        $konversi = [
            'A' => 4.0,
            'A-' => 3.7,
            'B+' => 3.3,
            'B' => 3.0,
            'B-' => 2.7,
            'C+' => 2.3,
            'C' => 2.0,
            'C-' => 1.7,
            'D' => 1.0,
            'E' => 0.0,
        ];
        return $konversi[strtoupper(trim($nhuruf))] ?? 0.0;
    }

    public static function getTopIpkStudents($facultyId, $academicYearId = null)
    {
        $academicYear = $academicYearId
            ? AcademicYear::find($academicYearId)
            : AcademicYear::where('status', 1)->first();

        if (!$academicYear) {
            return collect([]); // Return collection kosong jika tidak ada tahun akademik
        }

        // Ambil data nilai berdasarkan student_id
        $grades = self::where('academic_year_id', $academicYear->id)
            ->whereNotNull('nhuruf')
            ->whereHas('student.department', fn($q) => $q->where('faculty_id', $facultyId))
            ->with(['student', 'schedule.course'])
            ->get();

        // Hitung IPK di PHP
        $studentIpk = $grades->groupBy('student_id')->map(function ($grades) {
            $totalSks = $totalNilai = 0;

            foreach ($grades as $grade) {
                $sks = $grade->schedule->course->sks ?? 0;
                $nilai = self::convertNhurufToAngka($grade->nhuruf);

                if ($sks && $nilai !== null) {
                    $totalSks += $sks;
                    $totalNilai += $sks * $nilai;
                }
            }

            $ipk = $totalSks ? $totalNilai / $totalSks : 0;

            $student = $grades->first()->student;
            return [
                'name' => optional($student)->nama_mhs ?? 'Unknown',
                'ipk' => number_format($ipk, 2),
                'nim' => optional($student)->nim ?? 'Unknown',
                'department' => optional($student->department)->nama ?? 'Unknown'
            ];
        })->sortByDesc('ipk')->take(3)->values();

        return $studentIpk;
    }

}