<?php

namespace App\Helpers;

use App\Models\StudyPlan;
use App\Models\Student;
use App\Models\AcademicYear;

class KrsHelper
{
    public static function getActiveAcademicYearId(): ?int
    {
        return AcademicYear::where('status', 1)->value('id');
    }

    /**
     * Ambil daftar StudyPlan yang sudah disetujui untuk jadwal dan tahun akademik
     */
    public static function getApprovedStudyPlans(int $scheduleId, ?int $academicYearId = null)
    {
        $academicYearId = $academicYearId ?? self::getActiveAcademicYearId();

        return StudyPlan::where('schedule_id', $scheduleId)
            ->where('academic_year_id', $academicYearId)
            ->where('status', 'approved')
            ->get();
    }

    /**
     * Ambil ID mahasiswa dari StudyPlan yang disetujui
     */
    public static function getApprovedStudentIds(int $scheduleId, ?int $academicYearId = null): array
    {
        return self::getApprovedStudyPlans($scheduleId, $academicYearId)
            ->pluck('student_id')
            ->toArray();
    }

    /**
     * Ambil data mahasiswa yang mengambil KRS dan disetujui
     */
    public static function getApprovedStudents(int $scheduleId, ?int $academicYearId = null)
    {
        $studentIds = self::getApprovedStudentIds($scheduleId, $academicYearId);

        return Student::whereIn('id', $studentIds)->get();
    }

    public static function getApprovedStudyPlansWithStudents($scheduleId, $academicYearId)
    {
        return StudyPlan::where('schedule_id', $scheduleId)
            ->where('academic_year_id', $academicYearId)
            ->where('status', 'approved')
            ->with('student')
            ->get();
    }

    public static function formatTime($start, $end)
    {
        return sprintf('%s-%s', $start, $end);
    }

}