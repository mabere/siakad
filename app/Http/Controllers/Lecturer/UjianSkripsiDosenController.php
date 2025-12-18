<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\ThesisExam;
use App\Models\ThesisExamCriteria;
use App\Models\ThesisExamCriterium;
use App\Models\ThesisExamScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UjianSkripsiDosenController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $lecturer = Lecturer::where('user_id', $user->id)->first();
        if (!$lecturer) {
            return back()->with('error', 'Anda tidak terdaftar sebagai dosen.');
        }
        $exams = ThesisExam::whereHas('examiners', function ($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id);
        })->whereIn('status', ['dijadwalkan', 'pelaksanaan', 'selesai'])->get();
        return view('skripsi.dosen.index', compact('exams'));
    }
    public function show(ThesisExam $thesis_exam)
    {
        // Pastikan dosen yang login adalah penguji untuk ujian ini
        $user = Auth::user();
        $lecturer = Lecturer::where('user_id', $user->id)->first();

        if (!$lecturer || !$thesis_exam->examiners->contains('lecturer_id', $lecturer->id)) {
            abort(403, 'Akses ditolak. Anda bukan penguji untuk ujian ini.');
        }

        // --- Bagian yang Diperbaiki ---
        // Ambil jenis ujian dari kolom 'exam_type' di tabel thesis_exams
        $examType = $thesis_exam->exam_type;

        // Ambil kriteria penilaian berdasarkan jenis ujian
        $criterias = ThesisExamCriterium::where('exam_type', $examType)->get();

        // Ambil skor yang sudah diberikan oleh dosen ini jika ada
        $scores = ThesisExamScore::where('thesis_exam_id', $thesis_exam->id)
            ->where('lecturer_id', $lecturer->id)
            ->get();

        // --- Akhir Perbaikan ---

        return view('skripsi.dosen.show', compact('thesis_exam', 'criterias', 'scores', 'lecturer'));
    }

    public function storeScore(Request $request, ThesisExam $thesis_exam)
    {
        $user = Auth::user();
        $lecturer = Lecturer::where('user_id', $user->id)->first();
        if (!$lecturer || !$thesis_exam->examiners->contains('lecturer_id', $lecturer->id)) {
            abort(403, 'Akses ditolak. Anda bukan penguji untuk ujian ini.');
        }
        // 1. Validasi input
        $validated = $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|array',
            'notes.*' => 'nullable|string',
            'comment' => 'nullable|string',
        ]);
        // 2. Simpan atau perbarui skor
        foreach ($validated['scores'] as $criteria_id => $score) {
            ThesisExamScore::updateOrCreate(
                [
                    'thesis_exam_id' => $thesis_exam->id,
                    'lecturer_id' => $lecturer->id,
                    'criteria_id' => $criteria_id,
                ],
                [
                    'score' => $score,
                    'notes' => $validated['notes'][$criteria_id] ?? null,
                ]
            );
        }
        // 3. Simpan atau perbarui comment di tabel thesis_exam_examiners
        $examiner = $thesis_exam->examiners()->where('lecturer_id', $lecturer->id)->first();
        if ($examiner) {
            $examiner->update(['comment' => $validated['comment']]);
        }
        // 4. Perbarui status ujian jika semua penguji sudah memberi nilai
        $this->updateExamStatusIfAllScored($thesis_exam);
        return redirect()->route('nilai.examiner.exams.index')->with('success', 'Penilaian berhasil disimpan.');
    }

    public function getScores(ThesisExam $thesis_exam)
    {
        $lecturer = Auth::user()->lecturer;
        if (!$lecturer || !$thesis_exam->examiners->contains('lecturer_id', $lecturer->id)) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }
        $scores = ThesisExamScore::with('criteria')
            ->where('thesis_exam_id', $thesis_exam->id)
            ->where('lecturer_id', $lecturer->id)
            ->get();
        $comment = optional($thesis_exam->examiners->where('lecturer_id', $lecturer->id)->first())->comment;
        return response()->json([
            'scores' => $scores,
            'comment' => $comment,
        ]);
    }

    private function updateExamStatusIfAllScored(ThesisExam $thesis_exam)
    {
        $chairmanId = $thesis_exam->chairman_id;
        $secretaryId = $thesis_exam->secretary_id;
        $pengujiIds = $thesis_exam->examiners
            ->pluck('lecturer_id')
            ->reject(function ($lecturerId) use ($chairmanId, $secretaryId) {
                return $lecturerId == $chairmanId || $lecturerId == $secretaryId;
            });
        $totalPengujiCount = $pengujiIds->count();
        if ($totalPengujiCount === 0) {
            return;
        }
        $scoredPengujiCount = ThesisExamScore::where('thesis_exam_id', $thesis_exam->id)
            ->whereIn('lecturer_id', $pengujiIds)
            ->distinct('lecturer_id')
            ->count();

        if ($scoredPengujiCount >= $totalPengujiCount) {
            $thesis_exam->update(['status' => 'selesai']);
            $this->calculateFinalScore($thesis_exam);
        } else {
            $thesis_exam->update(['status' => 'pelaksanaan']);
        }
    }

    private function calculateFinalScore(ThesisExam $thesis_exam)
    {
        // 1. Identifikasi ID ketua dan sekretaris
        $excludedLecturers = [$thesis_exam->chairman_id, $thesis_exam->secretary_id];

        // 2. Ambil semua skor dari semua dosen, lalu saring (filter) yang bukan penguji
        $pengujiScores = ThesisExamScore::with('criteria')
            ->where('thesis_exam_id', $thesis_exam->id)
            ->whereNotIn('lecturer_id', $excludedLecturers)
            ->get();
        // Cek apakah ada skor penguji yang tersedia
        if ($pengujiScores->isEmpty()) {
            $thesis_exam->update(['final_score' => null]);
            return;
        }
        $finalScoresPerLecturer = [];
        // 3. Kelompokkan skor berdasarkan dosen penguji
        foreach ($pengujiScores->groupBy('lecturer_id') as $lecturerId => $scores) {
            $totalWeightedScore = 0;
            $totalWeight = 0;
            foreach ($scores as $score) {
                // Hitung skor berdasarkan bobot kriteria
                $totalWeightedScore += ($score->score * $score->criteria->weight);
                $totalWeight += $score->criteria->weight;
            }

            // Hitung nilai akhir per dosen (total skor / total bobot)
            $finalScoresPerLecturer[$lecturerId] = $totalWeight > 0 ? $totalWeightedScore / $totalWeight : 0;
        }

        // 4. Rata-ratakan nilai akhir dari semua dosen penguji
        $overallFinalScore = collect($finalScoresPerLecturer)->avg();

        // 5. Simpan nilai akhir ke tabel thesis_exams
        $thesis_exam->update(['final_score' => $overallFinalScore]);
    }

    // private function updateExamStatusIfAllScored(ThesisExam $thesis_exam)
    // {
    //     // Hitung jumlah penguji yang sudah memberi nilai
    //     $scoredExaminersCount = ThesisExamScore::where('thesis_exam_id', $thesis_exam->id)
    //         ->distinct('lecturer_id')
    //         ->count();
    //     // Hitung total penguji
    //     $totalExaminersCount = $thesis_exam->examiners->count();
    //     if ($scoredExaminersCount >= $totalExaminersCount) {
    //         // Perbarui status ujian menjadi 'selesai'
    //         $thesis_exam->update(['status' => 'selesai']);
    //         // Opsional: Lakukan penghitungan nilai akhir di sini
    //         $this->calculateFinalScore($thesis_exam);
    //     } else {
    //         // Jika belum semua, ubah status menjadi 'pelaksanaan'
    //         $thesis_exam->update(['status' => 'pelaksanaan']);
    //     }
    // }

    // private function calculateFinalScore(ThesisExam $thesis_exam)
    // {
    //     $allScores = ThesisExamScore::with('criteria')
    //         ->where('thesis_exam_id', $thesis_exam->id)
    //         ->get();
    //     // Cek apakah ada skor yang tersedia
    //     if ($allScores->isEmpty()) {
    //         return;
    //     }
    //     $finalScores = [];
    //     foreach ($allScores->groupBy('lecturer_id') as $lecturerId => $scoresByLecturer) {
    //         $totalScoreByLecturer = 0;
    //         foreach ($scoresByLecturer as $score) {
    //             // Hitung skor berdasarkan bobot kriteria
    //             $totalScoreByLecturer += ($score->score * $score->criteria->weight);
    //         }
    //         // Simpan nilai akhir per dosen, lalu dibagi 100 karena bobot dalam persen
    //         $finalScores[$lecturerId] = $totalScoreByLecturer / 100;
    //     }
    //     // Rata-rata nilai akhir dari semua dosen penguji
    //     $overallFinalScore = collect($finalScores)->avg();
    //     // Simpan nilai akhir ke tabel thesis_exams
    //     $thesis_exam->update(['final_score' => $overallFinalScore]);
    // }

}