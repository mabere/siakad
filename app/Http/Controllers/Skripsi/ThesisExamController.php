<?php

namespace App\Http\Controllers\Skripsi;

use App\Models\User;
use App\Models\Thesis;
use App\Models\Lecturer;
use App\Models\ThesisExam;
use Illuminate\Http\Request;
use App\Models\ThesisDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ThesisExamExaminer;
use App\Models\ThesisExamStatusLog;
use App\Http\Controllers\Controller;
use App\Services\ThesisPhaseService;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ThesisExamNotification;

class ThesisExamController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        $thesis = Thesis::where('student_id', $student->id)
            ->latest()
            ->with([
                'exams' => function ($query) {
                    $query->latest();
                }
            ])
            ->first();

        if (!$thesis) {
            return redirect()->back()->with('error', 'Data skripsi belum ditemukan.');
        }
        $latestExam = $thesis->exams->first();
        return view('backend.skripsi.mhs.exam-index', compact('thesis', 'latestExam'));
    }

    public function formRevision(Thesis $thesis)
    {
        $this->authorize('reviseExam', $thesis);

        return view('backend.skripsi.mhs.exam-revision-form', compact('thesis'));
    }

    public function submitRevision(Request $request, Thesis $thesis)
    {
        $this->authorize('reviseExam', $thesis);

        $request->validate([
            'lembar_persetujuan' => 'nullable|file|mimes:pdf|max:2048',
            'draft_skripsi' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        // Update dokumen yang diunggah ulang
        $docs = ['lembar_persetujuan', 'draft_skripsi'];

        foreach ($docs as $type) {
            if ($request->hasFile($type)) {
                $path = $request->file($type)->store('documents', 'public');
                $thesis->documents()->updateOrCreate(
                    ['document_type' => $type],
                    ['file_path' => $path, 'status' => 'pending']
                );
            }
        }

        // Reset revisi Jika revisi, update dokumen & status ujian
        $thesis->exam->update([
            'status' => 'diajukan',
            'revisi_notes' => null,
        ]);

        // Kirim notifikasi ke KTU
        $ktus = User::whereHas('roles', fn($q) => $q->where('name', 'ktu'))->get();

        foreach ($ktus as $ktu) {
            $ktu->notify(new ThesisExamNotification([
                'title' => 'Revisi Pengajuan Ujian',
                'message' => "Mahasiswa {$thesis->student->nama_mhs} telah mengirim revisi pendaftaran ujian skripsi.",
                'link' => route('ktu.thesis.exam.show', $thesis->id),
            ]));
        }

        return redirect()->route('student.thesis.exam.index', $thesis->id)->with('success', 'Dokumen revisi berhasil dikirim ulang.');
    }

    public function show(ThesisExam $exam)
    {
        $this->authorize('showDetail', $exam);

        $thesisExam = ThesisExam::where('id', $exam->id)->first();
        if (!$thesisExam) {
            return redirect()->route('student.thesis.exam.index')->with('error', 'Data pendaftaran ujian tidak ditemukan.');
        }

        $thesisExam->load([
            'thesis.supervisions.supervisor.user',
            'examiners.lecturer.user',
            'documents'
        ]);

        return view('backend.skripsi.mhs.exam-show', compact('thesisExam'));
    }

    // Metode untuk Kaprodi = Pemilihan Penguji
    public function indexForKaprodi()
    {
        $kaprodi = Auth::user()->lecturer;
        $departmentId = $kaprodi->department_id;
        if (!$departmentId) {
            return redirect()->route('dashboard')->with('error', 'Data Program Studi tidak ditemukan.');
        }
        $exams = ThesisExam::whereHas('thesis.student.department', function ($query) use ($departmentId) {
            $query->where('id', $departmentId);
        })
            ->with(['thesis.student.user', 'thesis.student.department', 'examiners.lecturer.user'])
            ->latest('updated_at')
            ->paginate(10);
        $lecturers = Lecturer::where('department_id', $departmentId)->get();
        return view('backend.skripsi.kaprodi.index', compact('exams', 'lecturers'));
    }

    public function showForKaprodi(ThesisExam $exam)
    {
        $exam->load([
            'thesis.student.user',
            'thesis.supervisions.supervisor.user',
            'examiners.lecturer.user',
            'documents',
        ]);
        $kaprodiDepartmentId = Auth::user()->lecturer->department_id;
        if ($exam->thesis->student->department->id !== $kaprodiDepartmentId) {
            return redirect()->route('kaprodi.thesis.exam.index')
                ->with('error', 'Anda tidak memiliki akses untuk melihat ujian ini.');
        }

        return view('backend.skripsi.kaprodi.show', compact('exam'));
    }


    public function formExaminers(ThesisExam $exam)
    {
        $this->authorize('assignExaminers', $exam);
        $kaprodi = Auth::user()->lecturer;
        // Tambahkan validasi defensif
        if (!$kaprodi) {
            return redirect()->back()->with('error', 'Data dosen Kaprodi tidak ditemukan.');
        }
        $lecturers = Lecturer::where('department_id', $kaprodi->department_id)->with('user')->get();

        // Ambil ID dosen pembimbing dari thesis
        $supervisors = $exam->thesis->supervisions->pluck('supervisor_id')->toArray();

        // Ambil ID penguji yang sudah ada
        $examiners = $exam->examiners->pluck('lecturer_id')->toArray();
        return view('backend.skripsi.admin.form-examiners', compact('exam', 'lecturers', 'examiners', 'supervisors'));
    }

    public function assignExaminers(Request $request, ThesisExam $exam)
    {
        $this->authorize('assignExaminers', $exam);
        $supervisors = $exam->thesis->supervisions->pluck('supervisor_id')->toArray();
        $examType = $exam->exam_type;
        if (in_array($examType, ['proposal', 'hasil'])) {
            $requiredExaminers = 2;
            $uniqueExaminersMessage = 'Dua penguji harus berbeda.';
        } else { // 'tutup'
            $requiredExaminers = 3;
            $uniqueExaminersMessage = 'Tiga penguji harus berbeda.';
        }
        $request->validate([
            'examiners' => [
                'required',
                'array',
                'size:' . $requiredExaminers,
                function ($attribute, $value, $fail) use ($supervisors, $requiredExaminers, $uniqueExaminersMessage) {
                    if (count(array_unique($value)) !== $requiredExaminers) {
                        $fail($uniqueExaminersMessage);
                    }
                    if (array_intersect($supervisors, $value)) {
                        $fail('Dosen pembimbing tidak boleh menjadi penguji.');
                    }
                }
            ],
            'examiners.*' => 'exists:lecturers,id',
        ]);
        $exam->examiners()->delete();
        foreach ($request->examiners as $lecturerId) {
            $exam->examiners()->create(['lecturer_id' => $lecturerId]);
        }
        $exam->update(['status' => 'penguji_ditetapkan']);
        $messageTemplate = [
            'dekan' => "Kaprodi telah menetapkan penguji skripsi untuk mahasiswa {$exam->thesis->student->user->name}.",
            'penguji' => "Anda ditetapkan sebagai penguji skripsi mahasiswa {$exam->thesis->student->user->name}.",
            'mahasiswa' => 'Penguji skripsi Anda telah ditetapkan oleh Kaprodi.',
        ];
        $dekans = User::whereHas('roles', fn($q) => $q->where('name', 'dekan'))->get();
        foreach ($dekans as $dekan) {
            $dekan->notify(new ThesisExamNotification([
                'title' => 'Penetapan Penguji',
                'message' => $messageTemplate['dekan'],
                'link' => route('dekan.thesis.exam.index'), // , $exam->id
            ]));
        }
        foreach ($exam->examiners as $examiner) {
            $examiner->lecturer->user->notify(new ThesisExamNotification([
                'title' => 'Penugasan Penguji Skripsi',
                'message' => $messageTemplate['penguji'],
                'link' => route('nilai.examiner.exams.index', $exam->id),
            ]));
        }
        $exam->thesis->student->user->notify(new ThesisExamNotification([
            'title' => 'Penguji Telah Ditetapkan',
            'message' => $messageTemplate['mahasiswa'],
            'link' => route('mahasiswa.thesis.exam.show', ['thesis' => $exam->thesis->id, 'exam' => $exam->id]),
        ]));
        return redirect()->route('kaprodi.thesis.exam.index')->with('success', 'Penguji berhasil ditetapkan.');
    }

    public function updateExaminers(Request $request, Thesis $thesis)
    {
        $this->authorize('assignExaminers', $thesis);
        $supervisors = $thesis->supervisions->pluck('supervisor_id')->toArray();
        $examType = $thesis->exam->exam_type;
        if (in_array($examType, ['proposal', 'hasil'])) {
            $requiredExaminers = 2;
            $uniqueExaminersMessage = 'Dua penguji harus berbeda.';
        } else { // 'tutup'
            $requiredExaminers = 3;
            $uniqueExaminersMessage = 'Tiga penguji harus berbeda.';
        }
        $request->validate([
            'examiners' => [
                'required',
                'array',
                'size:' . $requiredExaminers,
                function ($attribute, $value, $fail) use ($supervisors, $requiredExaminers, $uniqueExaminersMessage) {
                    // Periksa apakah jumlah penguji unik sesuai dengan jumlah yang dibutuhkan
                    if (count(array_unique($value)) !== $requiredExaminers) {
                        $fail($uniqueExaminersMessage);
                    }
                    // Periksa apakah dosen pembimbing tidak menjadi penguji
                    if (array_intersect($supervisors, $value)) {
                        $fail('Dosen pembimbing tidak boleh menjadi penguji.');
                    }
                }
            ],
            'examiners.*' => 'exists:lecturers,id',
        ]);
        $thesis->exam->examiners()->delete();
        foreach ($request->examiners as $lecturerId) {
            $thesis->exam->examiners()->create(['lecturer_id' => $lecturerId]);
        }
        $dekans = User::whereHas('roles', fn($q) => $q->where('name', 'dekan'))->get();
        foreach ($dekans as $dekan) {
            $dekan->notify(new ThesisExamNotification([
                'title' => 'Perubahan Penguji',
                'message' => "Kaprodi telah mengubah daftar penguji skripsi untuk mahasiswa {$thesis->student->nama_mhs}.",
                'link' => route('dekan.thesis.exam.show', $thesis->id),
            ]));
        }
        foreach ($thesis->exam->examiners as $examiner) {
            $examiner->lecturer->user->notify(new ThesisExamNotification([
                'title' => 'Penugasan Penguji Skripsi',
                'message' => "Anda ditunjuk sebagai penguji skripsi mahasiswa {$thesis->student->nama_mhs}.",
                'link' => route('kaprodi.thesis.exam.index', $thesis->id),
            ]));
        }

        return redirect()->route('kaprodi.thesis.exam.index')->with('success', 'Penguji berhasil diperbarui.');
    }

    // End Metode untuk Kaprodi = Pemilihan Penguji



    public function printSkPanitiaUjian()
    {
        $exams = ThesisExam::with(['thesis.student', 'examiners.lecturer'])
            ->where('status', 'dijadwalkan')
            ->orderBy('scheduled_at')
            ->get()
            ->groupBy('thesis.student.department_id');

        $pdf = Pdf::loadView('backend.skripsi.cetak-sk', compact('exams'));
        return $pdf->stream('sk-panitia-ujian.pdf');
    }

    public function scoreDetails(Thesis $thesis)
    {
        $this->authorize('viewScoreDetail', $thesis);

        $thesis->load('exam.examiners.lecturer.user', 'student');

        return view('backend.skripsi.kaprodi.score-details', compact('thesis'));
    }

    public function reviseScore(Request $request, Thesis $thesis, ThesisExamExaminer $examiner)
    {
        $this->authorize('reviseScore', $thesis);

        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'reason' => 'required|string|max:255',
        ]);

        $oldScore = $examiner->score;

        $examiner->update(['score' => $request->score]);

        // Rehitung nilai akhir
        $finalScore = $thesis->exam->examiners()->avg('score');
        $thesis->exam->update(['final_score' => $finalScore]);

        // Log revisi
        ThesisExamStatusLog::create([
            'thesis_exam_id' => $thesis->exam->id,
            'old_status' => 'nilai_' . $oldScore,
            'new_status' => 'nilai_' . $request->score,
            'notes' => 'Revisi nilai oleh Kaprodi untuk penguji: ' . $examiner->lecturer->nama_dosen .
                ' (alasan: ' . $request->reason . ')',
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);


        return back()->with('success', 'Nilai berhasil direvisi.');
    }


}