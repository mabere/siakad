<?php

namespace App\Services;

use App\Models\User;
use App\Models\Thesis;
use App\Models\ThesisExam;
use App\Models\ThesisDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Models\ThesisExamExaminer;
use App\Models\ThesisExamStatusLog;
use Illuminate\Support\Facades\Event;
use App\Events\ExamRegistered;
use App\Events\ExamScheduled;
use App\Events\ExaminersAssigned;

class ThesisExamService
{
    /**
     * Menangani logika pendaftaran ujian skripsi.
     *
     * @param Thesis $thesis
     * @param array<string, UploadedFile> $documents
     * @return ThesisExam
     */
    public function handleRegistration(Thesis $thesis, array $documents): ThesisExam
    {
        foreach ($documents as $type => $file) {
            $path = $file->store("documents/thesis-{$thesis->id}", 'public');
            ThesisDocument::updateOrCreate(
                ['thesis_id' => $thesis->id, 'document_type' => $type],
                ['file_path' => $path, 'status' => 'pending']
            );
        }
        $exam = $thesis->exam()->updateOrCreate(
            ['thesis_id' => $thesis->id],
            ['status' => ThesisExam::STATUS_DIAJUKAN]
        );

        // Update fase skripsi
        $thesis->update(['current_phase' => 'pemberkasan']);

        // Panggil event untuk notifikasi (Best Practice)
        // Event::dispatch(new ExamRegistered($thesis));

        return $exam;
    }

    /**
     * Menangani pengiriman revisi dokumen ujian.
     *
     * @param Thesis $thesis
     * @param array<string, UploadedFile> $documents
     * @return void
     */
    public function handleRevision(Thesis $thesis, array $documents): void
    {
        foreach ($documents as $type => $file) {
            if ($file) {
                $path = $file->store("documents/thesis-{$thesis->id}", 'public');
                $thesis->documents()->updateOrCreate(
                    ['document_type' => $type],
                    ['file_path' => $path, 'status' => 'pending']
                );
            }
        }

        // Reset status ujian
        $thesis->exam->update([
            'status' => ThesisExam::STATUS_DIAJUKAN,
            'revisi_notes' => null,
        ]);

        // Panggil event untuk notifikasi revisi (Best Practice)
        // Event::dispatch(new ExamRevisionSubmitted($thesis));
    }

    /**
     * Menetapkan atau memperbarui dosen penguji.
     *
     * @param Thesis $thesis
     * @param array $examinerIds
     * @return void
     */
    public function assignExaminers(Thesis $thesis, array $examinerIds): void
    {
        DB::transaction(function () use ($thesis, $examinerIds) {
            // Hapus penguji lama dan tambahkan yang baru
            $thesis->exam->examiners()->delete();

            $examinersData = array_map(fn($id) => ['lecturer_id' => $id], $examinerIds);
            $thesis->exam->examiners()->createMany($examinersData);

            // Update status & fase
            $thesis->exam->update(['status' => ThesisExam::STATUS_PENGUJI_DITETAPKAN]);
            $thesis->update(['current_phase' => 'penjadwalan']);
        });

        // Panggil event untuk notifikasi (Best Practice)
        // Event::dispatch(new ExaminersAssigned($thesis));
    }

    /**
     * Menyimpan jadwal ujian dan mencatat log.
     *
     * @param Thesis $thesis
     * @param array $scheduleData
     * @param User $changedBy
     * @return void
     */
    public function scheduleExam(Thesis $thesis, array $scheduleData, User $changedBy): void
    {
        $originalStatus = $thesis->exam->getOriginal('status');

        $thesis->exam->update([
            'scheduled_at' => $scheduleData['scheduled_at'],
            'location' => $scheduleData['location'],
            'chairman_id' => $scheduleData['chairman_id'],
            'secretary_id' => $scheduleData['secretary_id'],
            'status' => ThesisExam::STATUS_DIJADWALKAN,
        ]);

        $thesis->update(['current_phase' => 'pelaksanaan']);

        // Log perubahan status
        ThesisExamStatusLog::create([
            'thesis_exam_id' => $thesis->exam->id,
            'old_status' => $originalStatus,
            'new_status' => ThesisExam::STATUS_DIJADWALKAN,
            'notes' => 'Jadwal ujian telah ditetapkan.',
            'changed_by' => $changedBy->id,
        ]);

        // Panggil event untuk notifikasi (Best Practice)
        // Event::dispatch(new ExamScheduled($thesis));
    }

    /**
     * Memperbarui jadwal ujian (reschedule) tanpa mengubah status.
     *
     * @param Thesis $thesis
     * @param array $scheduleData
     * @return void
     */
    public function rescheduleExam(Thesis $thesis, array $scheduleData): void
    {
        $thesis->exam->update([
            'scheduled_at' => $scheduleData['scheduled_at'],
            'location' => $scheduleData['location'],
            'chairman_id' => $scheduleData['chairman_id'],
            'secretary_id' => $scheduleData['secretary_id'],
        ]);

        // Opsional: panggil event khusus untuk reschedule
        // Event::dispatch(new ExamRescheduled($thesis));
    }

    /**
     * Melakukan revisi nilai oleh Kaprodi dan menghitung ulang nilai akhir.
     *
     * @param ThesisExamExaminer $examiner
     * @param float $newScore
     * @param string $reason
     * @param User $changedBy
     * @return void
     */
    public function reviseExaminerScore(ThesisExamExaminer $examiner, float $newScore, string $reason, User $changedBy): void
    {
        $oldScore = $examiner->score;
        $thesisExam = $examiner->thesisExam;

        DB::transaction(function () use ($examiner, $thesisExam, $newScore, $oldScore, $reason, $changedBy) {
            // 1. Update nilai penguji
            $examiner->update(['score' => $newScore]);

            // 2. Hitung ulang nilai akhir
            $finalScore = $thesisExam->examiners()->avg('score');
            $thesisExam->update(['final_score' => $finalScore]);

            // 3. Catat log revisi
            ThesisExamStatusLog::create([
                'thesis_exam_id' => $thesisExam->id,
                'old_status' => 'nilai_' . $oldScore,
                'new_status' => 'nilai_' . $newScore,
                'notes' => "Revisi nilai oleh Kaprodi untuk penguji: {$examiner->lecturer->nama_dosen} (Alasan: {$reason})",
                'changed_by' => $changedBy->id,
            ]);
        });
    }
}