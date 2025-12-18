<?php

namespace App\Policies;

use App\Models\Curriculum;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CurriculumPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->activeRole(['kaprodi', 'admin', 'dosen']);
    }

    public function view(User $user, Curriculum $curriculum)
    {
        return $user->activeRole(['kaprodi', 'admin', 'dosen']) &&
            ($user->activeRole(['admin']) || $user->department_id === $curriculum->department_id);
    }

    public function create(User $user)
    {
        return $user->activeRole(['kaprodi', 'admin']);
    }

    public function update(User $user, Curriculum $curriculum)
    {
        return $user->activeRole(['kaprodi', 'admin']) &&
            ($user->activeRole('admin') || $user->department_id === $curriculum->department_id);
    }

    public function delete(User $user, Curriculum $curriculum)
    {
        return $user->activeRole('admin');
    }
}