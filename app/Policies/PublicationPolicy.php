<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Publication;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class PublicationPolicy
{
    use HandlesAuthorization;

    public function delete(User $user, Publication $publication): bool
    {
        // Misalnya, hanya admin atau penulis yang bisa menghapus publikasi
        return $user->role === 2;
    }
    public function update(User $user, Publication $publication): bool
    {
        if ($user->role === 1) {
            return true;
        }
        // Misalnya, hanya admin atau penulis yang bisa menghapus publikasi
        return $user->role === 2 && $publication->lecturers->contains('user_id', $user->id);
    }
}