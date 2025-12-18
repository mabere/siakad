<?php

namespace App\Services;

use App\Models\Thesis;

class ThesisPhaseService
{
    public static function evaluate(Thesis $thesis): void
    {
        $currentPhase = $thesis->current_phase;

        switch ($currentPhase) {
            case 'bimbingan_1':
                if (self::isSupervisorApproved($thesis, 'pembimbing_1')) {
                    $thesis->update(['current_phase' => 'bimbingan_2']);
                }
                break;

            case 'bimbingan_2':
                if (self::isSupervisorApproved($thesis, 'pembimbing_2')) {
                    $thesis->update(['current_phase' => 'pemberkasan']);
                }
                break;

            case 'pemberkasan':
                if (self::hasAllDocumentsApproved($thesis)) {
                    $thesis->update(['current_phase' => 'verifikasi_admin']);
                }
                break;

            case 'verifikasi_admin':
                if ($thesis->exam && $thesis->exam->examiners()->exists()) {
                    $thesis->update(['current_phase' => 'penetapan_penguji']);
                }
                break;

            case 'penetapan_penguji':
                if ($thesis->exam && $thesis->exam->scheduled_at) {
                    $thesis->update(['current_phase' => 'penjadwalan']);
                }
                break;

            case 'penjadwalan':
                if (self::hasExamScoresCompleted($thesis)) {
                    $thesis->update(['current_phase' => 'ujian_selesai']);
                }
                break;
        }
    }

    private static function isSupervisorApproved(Thesis $thesis, string $role): bool
    {
        return $thesis->supervisions
            ->where('supervisor_role', $role)
            ->first()
                ?->meetings()
            ->where('status', 'approved')
            ->exists();
    }

    private static function hasAllDocumentsApproved(Thesis $thesis): bool
    {
        return $thesis->documents()->count() > 0 &&
            $thesis->documents()->where('status', '!=', 'approved')->count() === 0;
    }

    private static function hasExamScoresCompleted(Thesis $thesis): bool
    {
        return $thesis->exam
            && $thesis->exam->examiners()->whereNull('score')->count() === 0;
    }
}
