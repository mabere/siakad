<?php

namespace App\Http\Controllers\Student;

use App\Models\User;
use App\Models\Thesis;
use App\Models\ThesisExam;
use App\Models\ThesisDocument;
use App\Enums\ThesisExamTypeEnum;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\RegisterExamRequest;
use App\Notifications\ThesisExamNotification;

class MahasiswaThesisController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;
        $theses = Thesis::where('student_id', $student->id)
            ->with([
                'supervisions.supervisor.user',
                'exams' => function ($query) {
                    $query->with(['documents', 'examiners.lecturer.user']);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('skripsi.mahasiswa.index', compact('theses'));
    }

    public function show(Thesis $thesis)
    {
        if (auth()->user()->student->id !== $thesis->student_id) {
            abort(403, 'Akses ditolak.');
        }

        // PERBAIKAN: Menggunakan relasi 'exams' (jamak)
        $thesis->load([
            'student.user',
            'supervisions.supervisor.user',
            'exams' => function ($query) {
                $query->with(['examiners.supervisor.user', 'documents']); // Memuat dokumen juga
            }
        ]);

        $supervisorsCompletedCount = $thesis->supervisions->where('status', 'completed')->count();
        $isSupervisionCompleted = $supervisorsCompletedCount === 2;

        return view('skripsi.mahasiswa.show', compact('thesis', 'isSupervisionCompleted'));
    }

    public function showExamRegistrationForm(Thesis $thesis)
    {
        $this->authorize('registerExam', $thesis);
        // Data ujian terakhir
        $latestExam = $thesis->exams()->latest()->first();
        $allowedExamTypes = [];
        $message = '';

        // Belum pernah mendaftar ujian
        if (!$latestExam) {
            $allowedExamTypes = ['proposal'];
            $message = 'Anda belum pernah mendaftar ujian. Silakan mendaftar ujian proposal.';
        } else {
            switch ($latestExam->status) {
                // Ujian dalam proses
                case 'diajukan':
                case 'dijadwalkan':
                case 'pelaksanaan':
                case 'selesai':
                    return redirect()->route('mahasiswa.thesis.show', $thesis->id)
                        ->with('info', 'Tahapan ujian Anda sedang berlangsung. Silakan tunggu hingga keputusan kelulusan diumumkan.');
                // Ujian tidak lulus
                case 'ditolak':
                    $allowedExamTypes = [$latestExam->exam_type];
                    $message = 'Anda tidak lulus ujian. Silakan perbaiki dan ujian ulang.';
                    break;
                // Lulus revisi
                case 'lulus':
                case 'lulus_revisi';
                    if ($latestExam->exam_type === 'proposal') {
                        $allowedExamTypes = ['hasil'];
                        $message = 'Ujian proposal telah lulus. Anda boleh mendaftar Ujian Hasil.';
                    } elseif ($latestExam->exam_type === 'hasil') {
                        $allowedExamTypes = ['hasil'];
                        $message = 'Ujian hasil telah lulus. Anda boleh mendaftar Ujian Skripsi.';
                    } else {
                        return redirect()->route('mahasiswa.thesis.show', $thesis->id)
                            ->with('info', 'Anda sudah menyelesaikan seluruh tahapan ujian Skripsi.');
                    }
                    break;
                // Tidak ada dalam status ujian
                default:
                    return redirect()->route('mahasiswa.thesis.show', $thesis->id)
                        ->with('error', 'Status ujian Anda tidak valid.');
            }
        }

        $examTypes = collect(ThesisExamTypeEnum::cases())->whereIn('value', $allowedExamTypes);
        return view('skripsi.mahasiswa.form_exam_registration', compact('thesis', 'examTypes'))->with('info', $message);
    }

    public function registerExam(RegisterExamRequest $request, Thesis $thesis)
    {
        DB::beginTransaction();
        try {
            // Ambil data ujian terakhir
            $latestExam = $thesis->exams()->latest()->first();
            $thesisExam = null;

            // Logika untuk menentukan apakah membuat record ujian baru atau mengupdate yang sudah ada
            if ($latestExam && $latestExam->status === 'ditolak') {
                // Skenario: Daftar ulang setelah ditolak. Perbarui status ujian yang ada.
                $thesisExam = $latestExam;
                $thesisExam->update([
                    'status' => 'diajukan',
                    'exam_type' => $request->exam_type // Pastikan tipe ujian tetap sama
                ]);
            } else {
                // Skenario: Pendaftaran pertama atau pendaftaran ujian berikutnya. Buat record baru.
                $thesisExam = $thesis->exams()->create([
                    'exam_type' => $request->exam_type,
                    'status' => 'diajukan',
                ]);
            }

            $docsToUpload = [
                'lembar_persetujuan' => $request->file('lembar_persetujuan'),
                'draft_skripsi' => $request->file('draft_skripsi'),
                'bukti_pembayaran' => $request->file('bukti_pembayaran'),
            ];

            // Loop untuk memproses setiap dokumen dan mengaitkannya dengan thesisExam->id
            foreach ($docsToUpload as $type => $file) {
                if ($file) {
                    $document = ThesisDocument::firstOrNew([
                        'thesis_exam_id' => $thesisExam->id,
                        'document_type' => $type
                    ]);

                    if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                        Storage::disk('public')->delete($document->file_path);
                    }

                    $path = $file->store('thesis_exams', 'public');
                    $document->fill([
                        'file_path' => $path,
                        'status' => 'pending',
                        'notes' => null,
                    ])->save();
                }
            }

            DB::commit();

            // Kirim notifikasi ke Admin
            $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();
            foreach ($admins as $admin) {
                $admin->notify(new ThesisExamNotification([
                    'title' => 'Pengajuan Ujian Baru',
                    'message' => "Mahasiswa {$thesis->student->nama_mhs} telah mengajukan ujian skripsi.",
                    'link' => route('admin.thesis.exam.show', $thesis->id)
                ]));
            }

            return redirect()->route('mahasiswa.thesis.show', $thesis->id)
                ->with('success', 'Pendaftaran ujian berhasil diajukan dan sedang menunggu verifikasi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mendaftar ujian. Silakan coba lagi. Error: ' . $e->getMessage());
        }
    }

    public function showExamDetails(Thesis $thesis, ThesisExam $exam)
    {
        // Cek otorisasi
        if (auth()->user()->student->id !== $thesis->student_id || $exam->thesis_id !== $thesis->id) {
            abort(403, 'Akses ditolak.');
        }

        $exam->load(['examiners.lecturer.user', 'documents']);

        // Tentukan tipe ujian berikutnya
        $nextExamType = null;
        if ($exam->exam_type === 'proposal') {
            $nextExamType = 'hasil';
        } elseif ($exam->exam_type === 'hasil') {
            $nextExamType = 'tutup';
        }

        // Periksa apakah ujian berikutnya sudah didaftarkan dan belum selesai
        $isNextExamRegistered = false;
        if ($nextExamType) {
            $registeredStatuses = [
                'pengajuan',
                'terverifikasi',
                'penguji_ditetapkan',
                'disetujui_dekan',
                'dijadwalkan',
                'pelaksanaan',
                'revisi'
            ];
            $isNextExamRegistered = ThesisExam::where('thesis_id', $thesis->id)
                ->where('exam_type', $nextExamType)
                ->whereIn('status', $registeredStatuses)
                ->exists();
        }

        return view('skripsi.mahasiswa.detail_ujian', compact('thesis', 'exam', 'isNextExamRegistered'));
    }


}
