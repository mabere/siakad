<?php

namespace App\Policies;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class KelasPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasAnyRole(['admin', 'staff']);
    }

    public function view(User $user, Kelas $kelas)
    {
        return $user->hasRole('admin') || ($user->hasRole('staff') && $kelas->department_id === $user->employee->department_id);
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['admin', 'staff']);
    }

    public function update(User $user, Kelas $kelas)
    {
        return $user->hasRole('admin') || ($user->hasRole('staff') && $kelas->department_id === $user->employee->department_id);
    }

    public function delete(User $user, Kelas $kelas)
    {
        return $user->hasRole('admin') || ($user->hasRole('staff') && $kelas->department_id === $user->employee->department_id);
    }
}
