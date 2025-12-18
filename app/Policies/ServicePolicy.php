<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Service;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServicePolicy
{
    use HandlesAuthorization;

    public function update(User $user, Service $service)
    {
        // Admin bisa mengupdate semua PKM
        if ($user->hasRole('admin')) {
            return true;
        }
        // Dosen hanya bisa mengupdate PKM miliknya
        return $service->lecturers->contains('id', $user->lecturer->id);
    }
    public function delete(User $user, Service $service)
    {
        // Logika yang sama dengan update
        if ($user->hasRole('admin')) {
            return true;
        }
        return $service->lecturers->contains('id', $user->lecturer->id);
    }
}