<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Thesis;
use App\Models\ThesisExam;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThesisExamPolicy
{
    use HandlesAuthorization;
    public function view(User $user, ThesisExam $exam): bool
    {
        $userRole = strtolower($user->activeRole());

        if (in_array($userRole, ['admin', 'kaprodi', 'dekan', 'ktu'])) {
            return true;
        }

        return $user->id === optional($exam->thesis->student)->user_id;
    }

    public function viewDetail(User $user, ThesisExam $thesisExam)
    {
        // Muat relasi secara eksplisit
        $thesisExam->load(['thesis.student.user']);

        $isAuthorized = $thesisExam->thesis && $thesisExam->thesis->student
            ? $user->id === $thesisExam->thesis->student->user_id
            : false;
        return $isAuthorized;
    }

    public function assignExaminers(User $user, ThesisExam $exam): bool
    {
        if (!$user->hasRole('kaprodi')) {
            return false;
        }

        // Ambil objek kaprodiLecturer secara terpisah
        $kaprodiLecturer = optional($exam->thesis->student->department)->kaprodiLecturer;

        // Periksa apakah ID user yang sedang login sama dengan ID user Kaprodi
        $isKaprodiForThisThesis = optional($kaprodiLecturer->user)->id === $user->id;

        $isStatusAllowed = in_array($exam->status, ['terverifikasi', 'penguji_ditetapkan', 'revisi_dekan']);

        return $isKaprodiForThisThesis && $isStatusAllowed;
    }

    public function approveByDekan(User $user, ThesisExam $exam): bool
    {
        return $user->hasRole('dekan') && in_array($exam->status, [
            ThesisExam::STATUS_PENGUJI_DITETAPKAN,
            ThesisExam::STATUS_REVISI_DEKAN
        ]);
    }

    public function showDetail(User $user, ThesisExam $exam)
    {
        $exam->load(['thesis.student.user']);

        $isAuthorized = $exam->thesis && $exam->thesis->student
            ? $user->id === $exam->thesis->student->user_id
            : false;
        return $isAuthorized;
    }

    public function viewSchedule(User $user, ThesisExam $exam): bool
    {
        $allowedStatuses = [
            ThesisExam::STATUS_DISETUJUI_DEKAN,
            ThesisExam::STATUS_DIJADWALKAN,
            ThesisExam::STATUS_DILAKSANAKAN,
            ThesisExam::STATUS_SELESAI
        ];

        return in_array(strtolower($user->activeRole()), ['kaprodi', 'admin', 'ktu']) &&
            in_array($exam->status, $allowedStatuses);
    }

    public function scheduleExam(User $user, ThesisExam $exam): bool
    {
        $allowedStatuses = [
            ThesisExam::STATUS_DISETUJUI_DEKAN,
            ThesisExam::STATUS_DIJADWALKAN
        ];
        return in_array(strtolower($user->activeRole()), ['kaprodi', 'admin', 'ktu']) &&
            in_array($exam->status, $allowedStatuses);
    }

    public function reschedule(User $user, ThesisExam $exam): bool
    {
        $allowedRoles = ['ktu', 'admin', 'kaprodi'];
        $isAuthorizedRole = in_array(strtolower($user->activeRole()), $allowedRoles);
        $isScheduled = $exam->status === ThesisExam::STATUS_DIJADWALKAN;
        return $isAuthorizedRole && $isScheduled;
    }

}