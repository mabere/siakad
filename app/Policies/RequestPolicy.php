<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LetterRequest;
use App\Models\LetterTypeAssignment;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequestPolicy
{
    use HandlesAuthorization;

    public function view(User $user, LetterRequest $letterRequest)
    {
        $roles = $user->roles()->pluck('name')->toArray();

        if (in_array('kaprodi', $roles) || in_array('staff', $roles)) {
            if (!$user->lecturer || !$user->lecturer->department) {
                Log::warning('User has no lecturer or department data for view', ['user_id' => $user->id]);
                return false;
            }

            $facultyId = $user->lecturer->department->faculty_id;
            $canView = LetterTypeAssignment::where('letter_type_id', $letterRequest->letter_type_id)
                ->where('faculty_id', $facultyId)
                ->exists();

            return $canView;
        }

        if (in_array('dekan', $roles)) {
            if (!$user->lecturer || !$user->lecturer->department) {
                return false;
            }
            $facultyId = $user->lecturer->department->faculty_id;
            $canView = LetterTypeAssignment::where('letter_type_id', $letterRequest->letter_type_id)
                ->where('faculty_id', $facultyId)
                ->exists();
            return $canView;
        }

        if (in_array('mahasiswa', $roles)) {
            $canView = $user->id === $letterRequest->user_id;
            return $canView;
        }

        if (in_array('dosen', $roles)) {
            $canView = $user->id === $letterRequest->user_id;
            return $canView;
        }

        return false;
    }

    public function approve(User $user, LetterRequest $letterRequest)
    {
        $userRoles = $user->roles()->pluck('name')->toArray();

        if ($letterRequest->status !== 'processing') {
            \Log::warning('Status not processing', ['status' => $letterRequest->status, 'letter_request_id' => $letterRequest->id]);
            return false;
        }

        if (!in_array('dekan', $userRoles)) {
            \Log::warning('User does not have dekan role', ['user_id' => $user->id, 'roles' => $userRoles]);
            return false;
        }

        if (!$user->lecturer || !$user->lecturer->department || !$user->lecturer->department->faculty_id) {
            \Log::warning('User has no lecturer or department data', ['user_id' => $user->id]);
            return false;
        }

        $facultyId = $user->lecturer->department->faculty_id;
        $assignment = LetterTypeAssignment::where('letter_type_id', $letterRequest->letter_type_id)
            ->where('faculty_id', $facultyId)
            ->first();

        if (!$assignment) {
            \Log::warning('No matching letter type assignment found', [
                'letter_type_id' => $letterRequest->letter_type_id,
                'faculty_id' => $facultyId,
            ]);
            return false;
        }

        // Check if approval_flow includes a step for 'dekan'
        $hasDekanStep = collect($assignment->approval_flow['steps'] ?? [])->contains(fn($step) => $step['role'] === 'dekan');
        if (!$hasDekanStep) {
            \Log::warning('Approval flow does not include dekan role', [
                'letter_type_id' => $letterRequest->letter_type_id,
                'approval_flow' => $assignment->approval_flow,
            ]);
            return false;
        }

        \Log::info('Approve authorization check', [
            'user_id' => $user->id,
            'faculty_id' => $facultyId,
            'letter_type_id' => $letterRequest->letter_type_id,
            'has_dekan_step' => $hasDekanStep,
        ]);

        return true;
    }

    // Review oleh Kaprodi
    public function review(User $user, LetterRequest $letterRequest)
    {
        $roles = $user->roles()->pluck('name')->toArray();

        if (!$user->hasAnyRole(['kaprodi', 'staff'])) {
            Log::warning('User does not have kaprodi/staff role', ['user_id' => $user->id]);
            return false;
        }

        if (!$user->lecturer || !$user->lecturer->department) {
            Log::warning('User has no lecturer or department data', ['user_id' => $user->id]);
            return false;
        }

        $facultyId = $user->lecturer->department->faculty_id;
        $assignment = LetterTypeAssignment::where('letter_type_id', $letterRequest->letter_type_id)
            ->where('faculty_id', $facultyId)
            ->where('department_id')
            ->exists();

        $canReview = $assignment && $letterRequest->status === 'submitted';

        if (!$canReview) {
            Log::warning('Review authorization failed', [
                'user_faculty_id' => $facultyId,
                'letter_type_id' => $letterRequest->letter_typ_id,
                'status' => $letterRequest->status,
                'assignment_exists' => $assignment,
            ]);
        }

        return $canReview;
    }

    public function updates(User $user, LetterRequest $letterRequest)
    {
        if ($user->roles()->where('name', 'admin')->exists()) {
            return true;
        }

        $isMahasiswa = $user->roles()->where('name', 'mahasiswa')->exists();
        $isOwner = $user->id === $letterRequest->user_id;
        $isEditableStatus = in_array($letterRequest->status, ['draft', 'submitted']);

        $isNotProcessed = !isset($letterRequest->approval_flow['review']) ||
            $letterRequest->approval_flow['review'] === 'pending';

        return $isMahasiswa && $isOwner && $isEditableStatus && $isNotProcessed;
    }

    public function update(User $user, LetterRequest $letterRequest)
    {
        if ($user->roles()->where('name', 'admin')->exists()) {
            return true;
        }

        $isMahasiswa = $user->roles()->where('name', 'mahasiswa')->exists();
        $isOwner = $user->id === $letterRequest->user_id;
        $isEditableStatus = in_array($letterRequest->status, ['draft', 'submitted']);

        return $isMahasiswa && $isOwner && $isEditableStatus;
    }

    public function delete(User $user, LetterRequest $letterRequest)
    {
        $isMahasiswa = $user->roles->contains('name', 'mahasiswa');
        return $isMahasiswa && $user->id === $letterRequest->user_id;
    }
}