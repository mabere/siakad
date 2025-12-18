<?php

namespace App\Http\Controllers\Skripsi;

use App\Models\User;
use App\Models\Thesis;
use App\Models\ThesisExam;
use Illuminate\Http\Request;
use App\Models\ThesisExamStatusLog;
use App\Http\Controllers\Controller;
use App\Notifications\ThesisExamNotification;

class ThesisApprovalController extends Controller
{
    public function index()
    {
        $exams = ThesisExam::with([
            'thesis.student.user',
            'thesis.supervisions.supervisor',
            'examiners.lecturer'
        ])
            ->latest('updated_at')
            ->paginate(15);
        return view('backend.skripsi.dekan.index', compact('exams'));
    }

    public function show(ThesisExam $exam)
    {
        $this->authorize('view', $exam);
        $exam->load([
            'thesis.student.user',
            'thesis.supervisions.supervisor.user',
            'examiners.lecturer.user',
        ]);

        return view('backend.skripsi.dekan.show', compact('exam'));
    }

    public function approve(ThesisExam $exam)
    {
        $this->authorize('approveByDekan', $exam);
        $oldStatus = $exam->status;
        $newStatus = 'disetujui_dekan';
        $exam->update(['status' => $newStatus]);
        ThesisExamStatusLog::create([
            'thesis_exam_id' => $exam->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => 'Disetujui oleh Dekan',
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);
        if ($exam->thesis->student && $exam->thesis->student->user) {
            $exam->thesis->student->user->notify(new ThesisExamNotification([
                'title' => 'Penguji Disetujui Dekan',
                'message' => 'Daftar penguji skripsi Anda telah disahkan oleh dekan.',
                'link' => route('mahasiswa.thesis.exam.show', ['thesis' => $exam->thesis->id, 'exam' => $exam->id]),
            ]));
        }
        foreach ($exam->examiners as $examiner) {
            $user = $examiner->lecturer->user ?? null;
            if ($user) {
                $user->notify(new ThesisExamNotification([
                    'title' => 'Penugasan Telah Disahkan',
                    'message' => "Penugasan Anda sebagai penguji skripsi mahasiswa {$exam->thesis->student->user->name} telah disetujui dekan.",
                    'link' => route('nilai.examiner.exams.show', $exam->id),
                ]));
            }
        }

        return redirect()->route('dekan.thesis.exam.index')->with('success', 'Penguji berhasil disetujui.');
    }


    public function revisi(Request $request, ThesisExam $exam)
    {
        $this->authorize('approveByDekan', $exam);

        $request->validate([
            'revisi_notes' => 'required|string|max:1000',
        ]);
        $oldStatus = $exam->status;
        $newStatus = 'revisi_dekan';

        $exam->update([
            'status' => $newStatus,
            'revisi_notes' => $request->revisi_notes,
        ]);

        ThesisExamStatusLog::create([
            'thesis_exam_id' => $exam->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => 'Permintaan revisi oleh Dekan: ' . $request->revisi_notes,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);

        // Notifikasi untuk Kaprodi
        $kaprodi = User::whereHas('roles', fn($q) => $q->where('name', 'kaprodi'))->first();
        if ($kaprodi) {
            $kaprodi->notify(new ThesisExamNotification([
                'title' => 'Permintaan Revisi Penguji',
                'message' => "Dekan meminta revisi untuk penguji skripsi mahasiswa {$exam->thesis->student->user->name}.",
                'link' => route('kaprodi.thesis.exam.show', $exam->id),
            ]));
        }

        return redirect()->route('dekan.thesis.exam.index')->with('success', 'Permintaan revisi berhasil dikirim.');
    }

}
