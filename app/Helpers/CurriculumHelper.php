<?php

namespace App\Helpers;

use App\Models\Course;
use App\Models\Curriculum;

class CurriculumHelper
{
    public static function getActiveCurriculum($departmentId, $academicYearId)
    {
        return Curriculum::where('department_id', $departmentId)
            ->where('academic_year_id', $academicYearId)
            ->where('status', 'active')
            ->first();
    }

    public static function formatCurriculumName(Curriculum $curriculum)
    {
        if (!$curriculum->academicYear) {
            return "{$curriculum->name} (TA tidak ditemukan)";
        }

        return "{$curriculum->name} ({$curriculum->academicYear->ta} {$curriculum->academicYear->semester})";
    }


    public static function formatPrerequisites($course)
    {
        // Jika model tidak memiliki properti prerequisites, maka langsung return '-'
        if (!property_exists($course, 'prerequisites') || empty($course->prerequisites)) {
            return '-';
        }

        $prerequisites = $course->prerequisites;

        // Jika prerequisites bukan array (atau JSON string), return '-'
        if (is_string($prerequisites)) {
            $prerequisites = json_decode($prerequisites, true);
        }

        if (!is_array($prerequisites) || empty($prerequisites)) {
            return '-';
        }

        // Ambil nama mata kuliah dari Course berdasarkan ID
        $prerequisiteCourses = Course::whereIn('id', array_filter($prerequisites, 'is_numeric'))
            ->pluck('name')
            ->toArray();

        return !empty($prerequisiteCourses) ? implode(', ', $prerequisiteCourses) : '-';
    }


}
