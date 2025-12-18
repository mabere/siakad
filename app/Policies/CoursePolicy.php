<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->activeRole(['admin', 'dekan', 'ujm', 'kaprodi', 'dosen', 'mahasiswa', 'staff', 'ktu']);
    }

    public function view(User $user, Department $department)
    {
        return $user->activeRole('admin')
            || ($user->activeRole(['kaprodi', 'staff']) && $user->department_id === $department->id)
            || ($user->activeRole('dekan') && $user->faculty_id === $department->faculty_id);
    }


    public function create(User $user)
    {
        return $user->activeRole(['admin', 'kaprodi', 'staff']);
    }

    public function update(User $user, Course $course)
    {
        return $user->activeRole('admin') ||
            ($user->activeRole(['kaprodi', 'staff']) && $course->department_id === $user->department_id);
    }

    public function delete(User $user, Course $course)
    {
        return $user->activeRole('admin') ||
            ($user->activeRole('kaprodi') && $course->department_id === $user->department_id);
    }

    public function import(User $user)
    {
        return $user->hasRole(['admin', 'kaprodi', 'staff']);
    }

    public function export(User $user)
    {
        return $user->hasRole(['admin', 'kaprodi', 'staff', 'dekan', 'ujm', 'ktu']);
    }
}
