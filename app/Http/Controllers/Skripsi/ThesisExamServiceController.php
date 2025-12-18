<?php

namespace App\Http\Controllers\Skripsi;

use App\Models\User;
use App\Models\Thesis;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use App\Models\ThesisDocument;
use App\Models\ThesisExamExaminer;
use App\Services\ThesisExamService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviseScoreRequest;
use App\Http\Requests\RegisterExamRequest;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\SubmitRevisionRequest;
use App\Http\Requests\AssignExaminersRequest;
use App\Notifications\ThesisExamNotification;

class ThesisExamServiceController extends Controller
{
    // 2. Inject service melalui constructor
    public function __construct(protected ThesisExamService $thesisExamService)
    {
    }

    public function indexForKaprodi()
    {
        $theses = Thesis::select('theses.*')
            ->join('thesis_exams', 'thesis_exams.thesis_id', '=', 'theses.id')
            ->with(['student', 'exam'])
            ->orderBy('thesis_exams.created_at', 'desc')
            ->get();
        $lecturers = Lecturer::all();
        return view('backend.skripsi.kaprodi.index', compact('theses', 'lecturers'));
    }

    public function showSchedule(Request $request, Thesis $thesis)
    {
        $this->authorize('viewSchedule', $thesis);

        $lecturers = Lecturer::with('user')->orderBy('nama_dosen')->get();

        $thesis->load([
            'exam.examiners.lecturer.user',
            'exam.chairman.user',
            'exam.secretary.user',
            'student.user',
            'supervisions.supervisor.user'
        ]);

        // Ambil ID dosen pembimbing
        $supervisors = $thesis->supervisions->pluck('supervisor_id')->toArray();

        // Ambil ID penguji dari input request jika ada (untuk validasi atau keperluan lain)
        $examiners = $request->input('examiners', []);
        return view('backend.skripsi.ktu.list-jadwal-ujian', compact('thesis', 'lecturers', 'supervisors'));
    }

    public function formRegister(Thesis $thesis)
    {
        $this->authorize('registerExam', $thesis);
        // Blokir jika sudah mendaftar (dan bukan status revisi/ditolak)
        if ($thesis->exam && !in_array($thesis->exam->status, ['revisi', 'ditolak'])) {
            return redirect()->route('student.thesis.exam.index', $thesis->id)
                ->with('error', 'Pengajukan pendaftaran ujian hanya dibolehkan satu kali. Silakan periksa statusnya.');
        }

        return view('backend.skripsi.mhs.exam-form', compact('thesis'));
    }

    // Metode penjadwalan ujian
    public function formSchedule(Thesis $thesis)
    {
        $this->authorize('scheduleExam', $thesis); // pastikan ada policy
        $lecturers = Lecturer::orderBy('nama_dosen')->get();
        return view('backend.skripsi.ktu.schedule-form', compact('thesis', 'lecturers'));
    }

    public function formExaminers(Thesis $thesis)
    {
        $this->authorize('assignExaminers', $thesis);

        $lecturers = Lecturer::with('user')->get();
        $supervisors = $thesis->supervisions->pluck('supervisor_id')->toArray();
        $examiners = $thesis->exam->examiners->pluck('lecturer_id')->toArray();

        return view('backend.skripsi.admin.form-examiners', compact('thesis', 'lecturers', 'supervisors'));
    }


    public function formRevision(Thesis $thesis)
    {
        $this->authorize('reviseExam', $thesis);

        return view('backend.skripsi.mhs.exam-revision-form', compact('thesis'));
    }

    public function index()
    {
        $student = auth()->user()->student;

        $thesis = Thesis::with(['exam', 'documents'])
            ->where('student_id', $student->id)
            ->latest()
            ->first();

        if (!$thesis) {
            return redirect()->back()->with('error', 'Data skripsi belum ditemukan.');
        }

        return view('backend.skripsi.mhs.exam-index', compact('thesis'));
    }
    /**
     * Menyimpan pendaftaran ujian baru.
     */
    public function registerExam(RegisterExamRequest $request, Thesis $thesis)
    {
        $this->authorize('registerExam', $thesis);

        $documents = [
            'lembar_persetujuan' => $request->file('lembar_persetujuan'),
            'draft_skripsi' => $request->file('draft_skripsi'),
        ];

        $this->thesisExamService->handleRegistration($thesis, $documents);

        // Notifikasi bisa dipindah ke Event Listener
        // ...

        return redirect()->route('student.thesis.exam.index', $thesis->id)
            ->with('success', 'Pendaftaran ujian berhasil diajukan.');
    }

    /**
     * Menyimpan revisi dokumen ujian.
     */
    public function submitRevision(SubmitRevisionRequest $request, Thesis $thesis)
    {
        $this->authorize('reviseExam', $thesis);

        $documents = [
            'lembar_persetujuan' => $request->file('lembar_persetujuan'),
            'draft_skripsi' => $request->file('draft_skripsi'),
        ];

        $this->thesisExamService->handleRevision($thesis, $documents);

        // Notifikasi bisa dipindah ke Event Listener
        // ...

        return redirect()->route('student.thesis.exam.index', $thesis->id)
            ->with('success', 'Dokumen revisi berhasil dikirim ulang.');
    }

    /**
     * Menetapkan dosen penguji skripsi.
     */
    public function assignExaminers(AssignExaminersRequest $request, Thesis $thesis)
    {
        $this->thesisExamService->assignExaminers(
            $thesis,
            $request->validated()['examiners']
        );

        // Notifikasi bisa dipindah ke Event Listener
        // ...

        return redirect()->route('kaprodi.thesis.exam.index')
            ->with('success', 'Penguji berhasil ditetapkan.');
    }

    /**
     * Menyimpan jadwal ujian.
     */
    public function storeSchedule(StoreScheduleRequest $request, Thesis $thesis)
    {
        $this->authorize('scheduleExam', $thesis);

        $this->thesisExamService->scheduleExam(
            $thesis,
            $request->validated(),
            $request->user() // Mengirim user yang sedang login
        );

        // Notifikasi bisa dipindah ke Event Listener
        // ...

        return redirect()->route('schedule.show', $thesis->id)
            ->with('success', 'Jadwal ujian berhasil disimpan.');
    }

    /**
     * Memperbarui nilai penguji.
     */
    public function reviseScore(ReviseScoreRequest $request, Thesis $thesis, ThesisExamExaminer $examiner)
    {
        $this->authorize('reviseScore', $thesis);

        $this->thesisExamService->reviseExaminerScore(
            $examiner,
            $request->validated('score'),
            $request->validated('reason'),
            $request->user()
        );

        return back()->with('success', 'Nilai berhasil direvisi.');
    }
}
