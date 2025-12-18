<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LetterRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\HandlesAuthorization;

class LetterRequestPolicy
{
    use HandlesAuthorization;

    public function view(User $user, LetterRequest $letterRequest)
    {
        // Admin bisa melihat semua surat
        if ($user->hasRole('admin')) {
            return true;
        }

        // Dekan hanya bisa melihat surat dari fakultasnya sendiri
        if ($user->hasRole('dekan')) {
            return $user->lecturer->department->faculty_id === $letterRequest->user->student->department->faculty_id;
        }

        // Mahasiswa hanya bisa melihat surat miliknya
        return $user->id === $letterRequest->user_id;
    }

    public function create(User $user)
    {
        // Hanya izinkan jika user memiliki role 'mahasiswa'
        return $user->hasRole('mahasiswa');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LetterRequest $letterRequest)
    {// Admin can update all requests
        if ($user->roles()->where('name', 'admin')->exists()) {
            return true;
        }

        // Users can only update their own requests if the request is editable
        return $user->id === $letterRequest->user_id && $letterRequest->isEditable();
    }

    public function delete(User $user, LetterRequest $letterRequest)
    {
        // Admin can delete any request
        if ($user->roles()->where('name', 'admin')->exists()) {
            return true;
        }

        // Dekan hanya bisa melihat surat dari fakultasnya sendiri
        if ($user->hasRole('dekan')) {
            return $user->lecturer->department->faculty_id === $letterRequest->user->student->department->faculty_id;
        }

        // Users can delete their own requests if in a deletable status
        return $user->id === $letterRequest->user_id && in_array($letterRequest->status, ['draft', 'submitted']);

    }
}