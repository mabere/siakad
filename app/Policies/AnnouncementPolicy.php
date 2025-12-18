<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnnouncementPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Announcement $announcement): bool
    {
        if (!$announcement->is_active) {
            return false;
        }

        $role = $user->activeRole();

        if ($role === 'dosen') {
            return $user->lecturer
                && $announcement->faculty_id == $user->lecturer->faculty_id
                && in_array($announcement->target_role, ['semua', 'dosen']);
        }

        if ($role === 'mahasiswa') {
            return $user->student
                && (
                    $announcement->department_id === null
                    || $announcement->department_id == $user->student->department_id
                )
                && (
                    $announcement->kelas_id === null
                    || $announcement->kelas_id == $user->student->kelas_id
                )
                && in_array($announcement->target_role, ['semua', 'mahasiswa']);
        }
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->activeRole(), ['admin', 'dekan', 'kaprodi', 'staff']);
    }

    public function update(User $user, Announcement $announcement): bool
    {
        $role = $user->activeRole();

        if ($role === 'admin') {
            return true;
        }

        if ($role === 'dekan') {
            // 1) Pemilik boleh edit
            if ($announcement->created_by === $user->id) {
                return true;
            }

            // 2) Cek pembuatnya dan role-nya
            $creator = $announcement->createdBy;
            $creatorRole = $creator ? $creator->activeRole() : null;

            // Jika pembuat adalah admin, tolak
            if ($creatorRole === 'admin') {
                return false;
            }

            // Kalau bukan admin, cek fakultas sama
            return $announcement->faculty_id === $user->lecturer->faculty_id;
        }

        if ($role === 'kaprodi') {
            return $user->lecturer && $announcement->department_id === $user->lecturer->department_id;
        }

        if ($role === 'staff') {
            return $user->employee && $announcement->department_id === $user->employee->department_id;
        }

        return false;
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        // Konsisten sama dengan update
        return $this->update($user, $announcement);
    }




    public function toggle(User $user, Announcement $announcement): bool
    {
        return $announcement->created_by === $user->id
            || in_array($user->activeRole(), ['admin']);
    }
}