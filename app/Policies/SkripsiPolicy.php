<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Thesis;
use App\Models\ThesisExam;
use Illuminate\Auth\Access\HandlesAuthorization;

class SkripsiPolicy
{
    use HandlesAuthorization;

    /**
     * Mahasiswa hanya boleh melihat skripsi milik sendiri
     */
    public function view(User $user, Thesis $thesis): bool
    {
        // Mahasiswa pemilik skripsi
        if ($user->id === optional($thesis->student)->user_id) {
            return true;
        }

        // Role lain yang boleh melihat skripsi
        return in_array($user->activeRole(), ['admin', 'kaprodi', 'dekan', 'ktu']);
    }

    public function showDetail(User $user, Thesis $thesis)
    {
        $thesis->load(['student.user']);

        $isAuthorized = $thesis->student
            ? $user->id === $thesis->student->user_id
            : false;
        return $isAuthorized;
    }

    public function registerExam(User $user, Thesis $thesis): bool
    {
        $isOwner = $user->id === optional($thesis->student)->user_id;
        $supervisorsCompleted = $thesis->supervisions()
            ->where('status', 'completed')
            ->count() === 2;

        return $isOwner && $supervisorsCompleted;
    }

    public function reviseExam(User $user, Thesis $thesis): bool
    {
        return $user->id === $thesis->student->user_id &&
            $thesis->exam &&
            $thesis->exam->status === 'revisi';
    }

    /**
     * Kaprodi/admin hanya bisa tetapkan penguji di fase penetapan_penguji
     */
    public function assignExaminers(User $user, ThesisExam $exam): bool
    {
        // 1. Pastikan user memiliki peran Kaprodi
        if (!$user->hasRole('kaprodi')) {
            return false;
        }
        // 2. Pastikan user adalah Kaprodi yang benar untuk departemen ini
        $isKaprodiForThisThesis = optional($exam->thesis->student->department)->kaprodi->user->id === $user->id;
        // 3. Pastikan status ujian memungkinkan penetapan penguji
        $isStatusAllowed = in_array($exam->status, ['terverifikasi', 'penguji_ditetapkan', 'revisi_dekan']);
        
        return $isKaprodiForThisThesis && $isStatusAllowed;
    }

    /**
     * Mahasiswa bisa mengajukan ujian jika di fase pemberkasan dan semua dokumen sudah disetujui
     */
    public function createExam(User $user, Thesis $thesis): bool
    {
        return $user->id === $thesis->student->user_id &&
            $thesis->current_phase === 'pemberkasan' &&
            $thesis->documents()->where('status', '!=', 'approved')->count() === 0;
    }

    /**
     * Mahasiswa bisa upload dokumen saat fase pemberkasan
     */
    public function uploadDocument(User $user, Thesis $thesis): bool
    {
        return $user->id === $thesis->student->user_id &&
            $thesis->current_phase === 'pemberkasan';
    }

    /**
     * Staff/admin hanya bisa verifikasi jika di fase verifikasi_admin
     */
    public function verifyDocument(User $user, Thesis $thesis): bool
    {
        return $user->hasAnyRole(['staff', 'admin']) &&
            $thesis->current_phase === 'verifikasi_admin';
    }


    /**
     * Admin hanya bisa jadwalkan ujian jika sudah ada penguji
     */
    public function scheduleExam(User $user, Thesis $thesis): bool
    {
        return in_array($user->activeRole(), ['kaprodi', 'admin', 'ktu']) &&
            in_array($thesis->exam?->status, ['disetujui_dekan', 'dijadwalkan', 'pelaksanaan', 'selesai']);
    }


    public function viewSchedule(User $user, Thesis $thesis): bool
    {
        return in_array($user->activeRole(), ['kaprodi', 'admin', 'ktu']) &&
            in_array($thesis->exam?->status, ['disetujui_dekan', 'dijadwalkan', 'pelaksanaan', 'selesai']);
    }

    public function reschedule(User $user, Thesis $thesis): bool
    {
        return $user->hasRole('ktu') && $thesis->current_phase === 'penjadwalan';
    }

    /**
     * Dosen penguji bisa input nilai saat fase penjadwalan
     */
    public function inputScore(User $user, Thesis $thesis): bool
    {
        return $user->hasRole('dosen') &&
            $thesis->exam &&
            $thesis->exam->examiners()->whereHas('lecturer', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->exists() &&
            $thesis->current_phase === 'penjadwalan' || $thesis->current_phase === 'pelaksanaan' || $thesis->current_phase === 'ujian_selesai';
    }

    public function reviseScore(User $user, Thesis $thesis)
    {
        return $user->hasRole('kaprodi') && $thesis->current_phase === 'ujian_selesai';
    }

    public function viewScoreDetail(User $user, Thesis $thesis): bool
    {
        return in_array($user->activeRole(), ['kaprodi', 'dekan']);
    }


}