<?php

namespace App\Http\Controllers\Lecturer;

use App\Models\User;
use App\Models\Thesis;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\ThesisExamNotification;

class ThesisExamController extends Controller
{
    public function inputScoreForm(Thesis $thesis)
    {
        $this->authorize('inputScore', $thesis);

        $lecturerId = auth()->user()->lecturer->id;

        $examiner = $thesis->exam->examiners()
            ->where('lecturer_id', $lecturerId)
            ->firstOrFail();

        return view('backend.skripsi.dosen.input-score', compact('thesis', 'examiner'));
    }

    public function storeScore(Request $request, Thesis $thesis)
    {
        $this->authorize('inputScore', $thesis);

        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $lecturerId = auth()->user()->lecturer->id;

        $examiner = $thesis->exam->examiners()
            ->where('lecturer_id', $lecturerId)
            ->firstOrFail();

        $examiner->update(['score' => $request->score]);

        // Cek apakah semua penguji sudah input nilai
        $totalExaminers = $thesis->exam->examiners()->count();
        $submitted = $thesis->exam->examiners()->whereNotNull('score')->count();

        if ($submitted === $totalExaminers) {
            $finalScore = $thesis->exam->examiners()->avg('score');

            $thesis->exam->update([
                'final_score' => $finalScore,
                'status' => 'selesai',
            ]);

            $thesis->update([
                'current_phase' => 'ujian_selesai',
            ]);

            // Kirim notifikasi ke mahasiswa
            $thesis->student->user->notify(new ThesisExamNotification([
                'title' => 'Nilai Akhir Ujian Skripsi',
                'message' => "Ujian skripsi Anda telah selesai. Nilai akhir: {$finalScore}.",
                'link' => route('student.thesis.exam.index', $thesis->id),
            ]));

            // Opsional: notifikasi ke kaprodi dan dekan
            $kaprodis = User::whereHas('roles', fn($q) => $q->where('name', 'kaprodi'))->get();
            $dekans = User::whereHas('roles', fn($q) => $q->where('name', 'dekan'))->get();

            foreach ($kaprodis->merge($dekans) as $user) {
                $user->notify(new ThesisExamNotification([
                    'title' => 'Ujian Skripsi Telah Selesai',
                    'message' => "Ujian mahasiswa {$thesis->student->nama_mhs} telah selesai dengan nilai akhir: {$finalScore}.",
                    'link' => route('dashboard'),
                ]));
            }
        }

        return redirect()->route('lecturer.exam.score.form', $thesis->id)->with('success', 'Nilai berhasil disimpan.');
    }

    public function reviseScore(Request $request, Thesis $thesis, $examinerId)
    {
        $this->authorize('reviseScore', $thesis);

        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'reason' => 'nullable|string|max:1000',
        ]);

        $examiner = $thesis->exam->examiners()->where('id', $examinerId)->firstOrFail();

        // Simpan log
        // ScoreRevisionLog::create([
        //     'thesis_exam_examiner_id' => $examiner->id,
        //     'old_score' => $examiner->score,
        //     'new_score' => $request->score,
        //     'revised_by' => auth()->id(),
        //     'reason' => $request->reason,
        //     'revised_at' => now(),
        // ]);

        // Update nilai
        $examiner->update(['score' => $request->score]);

        // Update final score
        $finalScore = $thesis->exam->examiners()->avg('score');
        $thesis->exam->update(['final_score' => $finalScore]);

        return back()->with('success', 'Nilai penguji berhasil direvisi.');
    }


}
