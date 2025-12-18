<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{
    ThesisSupervision,
    SupervisionMeeting,
    Thesis,
    ThesisDocument,
    ThesisExam,
    ThesisExamExaminer
};
use Carbon\Carbon;

class ThesisModuleSeeder extends Seeder
{
    public function run(): void
    {
        // $studentId = 62;
        $thesisId = 1;
        $lecturer1Id = 35;
        $lecturer2Id = 36;

        // Dokumen pemberkasan
        ThesisDocument::updateOrCreate([
            'thesis_id' => $thesisId,
            'document_type' => 'draft_skripsi',
        ], [
            'file_path' => 'documents/draft.pdf',
            'status' => 'approved',
        ]);

        ThesisDocument::updateOrCreate([
            'thesis_id' => $thesisId,
            'document_type' => 'lembar_persetujuan',
        ], [
            'file_path' => 'documents/acc.pdf',
            'status' => 'approved',
        ]);

        // Buat jadwal ujian
        $exam = ThesisExam::updateOrCreate([
            'thesis_id' => $thesisId,
        ], [
            'scheduled_at' => now()->addWeek(),
            'location' => 'Ruang Sidang A',
            'status' => 'dijadwalkan',
        ]);

        // Tambahkan penguji jika belum
        foreach ([$lecturer1Id, $lecturer2Id] as $lecturerId) {
            ThesisExamExaminer::firstOrCreate([
                'thesis_exam_id' => $exam->id,
                'lecturer_id' => $lecturerId,
            ]);
        }
    }

    private function createSupervisionIfNotExist($studentId, $thesisId, $lecturerId, $role)
    {
        ThesisSupervision::firstOrCreate([
            'student_id' => $studentId,
            'thesis_id' => $thesisId,
            'supervisor_id' => $lecturerId,
            'supervisor_role' => $role,
        ], [
            'assigned_at' => now()->subMonths(2),
            'status' => 'active',
        ]);
    }
}
