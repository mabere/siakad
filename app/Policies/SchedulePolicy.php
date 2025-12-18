<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Schedule;
use App\Models\Department;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchedulePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        \Log::debug('SchedulePolicy::viewAny', [
            'user_id' => $user->id,
            'admin_role' => $user->hasRole('admin'),
            'staff_role' => $user->hasRole('staff'),
        ]);

        return $user->hasRole('admin') || $user->hasRole('staff');
    }

    public function view(User $user, $resource)
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('staff')) {
            if (!$user->employee) {
                \Log::warning('SchedulePolicy::view - No employee data for user', ['user_id' => $user->id]);
                return false;
            }

            if ($resource instanceof Schedule) {
                return $user->employee->department_id === $resource->department_id;
            }

            if ($resource instanceof Department) {
                return $user->employee->department_id === $resource->id;
            }

            return false;
        }

        return false;
    }

    public function create(User $user, Department $department = null)
    {
        $role = $user->activeRole();

        // ✅ ADMIN bebas akses — langsung izinkan
        if ($role === 'admin') {
            return true;
        }

        // ✅ STAFF hanya boleh untuk departemennya sendiri (dan hanya jika ada relasi employee)
        if ($role === 'staff') {
            return $user->employee
                && (!$department || $user->employee->department_id === $department->id);
        }

        // ❌ Role lain tidak diperbolehkan
        return false;
    }




    public function update(User $user, Schedule $schedule)
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('staff')) {
            return $user->employee && $user->employee->department_id === $schedule->department_id;
        }

        return false;
    }

    public function delete(User $user, Schedule $schedule)
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('staff')) {
            return $user->employee && $user->employee->department_id === $schedule->department_id;
        }

        return false;
    }
}
