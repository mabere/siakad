<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Grade;
use App\Models\Course;
use App\Models\MkduCourse;


class GradePolicy
{
    public function validatedByDosen(User $user, Grade $grade): bool
    {
        if (!$user->hasRole('dosen')) {
            return false;
        }
        if (!$grade->schedule->lecturersInSchedule->contains('id', $user->lecturer->id)) {
            return false;
        }
        return $grade->validation_status === 'pending';
    }

    public function approveByProdi(User $user, Grade $grade): bool
    {
        if (!$user->hasRole('kaprodi')) {
            return false;
        }
        if ($grade->validation_status !== 'dosen_validated') {
            return false;
        }
        $schedulable = $grade->schedule->schedulable;
        $isKaprodiForProdiCourse = false;
        $isKaprodiForMkduCourse = false;
        if ($schedulable instanceof Course) {
            $isKaprodiForProdiCourse = $user->lecturer && ($user->lecturer->department_id === $schedulable->department_id);
        } elseif ($schedulable instanceof MkduCourse) {
            $isKaprodiForMkduCourse = true;
        }
        return $isKaprodiForProdiCourse || $isKaprodiForMkduCourse;
    }

    public function lockGrades(User $user, Grade $grade): bool
    {
        if (!$user->hasRole('staff')) {
            return false;
        }
        if ($grade->validation_status !== 'kaprodi_approved') {
            return false;
        }
        $schedulable = $grade->schedule->schedulable;
        $isStaffForProdiCourse = false;
        $isStaffForMkduCourse = false;
        if ($schedulable instanceof Course) {
            $isStaffForProdiCourse = $user->employee && ($user->employee->department_id === $schedulable->department_id);
        } elseif ($schedulable instanceof MkduCourse) {
            $isStaffForMkduCourse = true;
        }
        return $isStaffForProdiCourse || $isStaffForMkduCourse;
    }

    public function viewValidationPage(User $user): bool
    {
        return $user->hasRole(['staff', 'kaprodi']);
    }

}