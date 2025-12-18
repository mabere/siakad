<?php

namespace App\Http\Controllers\Admin;

use App\Models\ThesisExam;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\ThesisExamNotification;

class ReviewNilaiUjianController extends Controller
{
    public function index()
    {
        $finishedExams = ThesisExam::whereIn('status', ['selesai', 'lulus', 'lulus_revisi', 'ditolak'])
            ->with([
                'thesis.student.user',
                'chairman',
                'secretary',
                'examiners.lecturer'
            ])
            ->latest()
            ->get();
        return view('admin.review.index', compact('finishedExams'));
    }

    public function show(ThesisExam $thesis_exam)
    {
        $thesis_exam->load([
            'thesis.student.user',
            'chairman',
            'secretary',
            'examiners.lecturer',
            'scores.lecturer',
            'scores.criteria',
        ]);
        $scoresByLecturer = $thesis_exam->scores->groupBy('lecturer_id');
        return view('admin.review.show', compact('thesis_exam', 'scoresByLecturer'));
    }

    public function decide(Request $request, ThesisExam $thesis_exam)
    {
        // 1. Validasi input
        $validated = $request->validate([
            'final_status' => 'required|in:lulus,lulus_revisi,ditolak',
            'revisi_notes' => 'nullable|string|required_if:final_status,lulus_revisi,ditolak',
        ]);

        // 2. Simpan status dan catatan revisi saat ini
        $oldStatus = $thesis_exam->status;

        // 3. Update status dan catatan revisi di database
        $thesis_exam->update([
            'status' => $validated['final_status'],
            'revisi_notes' => $validated['revisi_notes'],
        ]);

        // 4. Kirim notifikasi jika status berubah dari 'selesai'
        if ($oldStatus === 'selesai') {
            // Ambil data mahasiswa dari relasi
            $student = $thesis_exam->thesis->student->user;

            $notificationData = [];

            switch ($validated['final_status']) {
                case 'lulus':
                    $notificationData = [
                        'title' => 'Keputusan Ujian Tugas Akhir Telah Dibuat',
                        'message' => 'Selamat, ujian tugas akhir Anda telah dinyatakan LULUS TANPA REVISI.',
                        'link' => route('mahasiswa.thesis.show', $thesis_exam->thesis->id),
                        'notes' => null,
                    ];
                    break;
                case 'lulus_revisi':
                    $notificationData = [
                        'title' => 'Keputusan Ujian Tugas Akhir Telah Dibuat',
                        'message' => 'Ujian tugas akhir Anda dinyatakan LULUS DENGAN REVISI. Silakan periksa catatan revisi.',
                        'link' => route('mahasiswa.thesis.show', $thesis_exam->thesis->id),
                        'notes' => $validated['revisi_notes'],
                    ];
                    break;
                case 'ditolak':
                    $notificationData = [
                        'title' => 'Keputusan Ujian Tugas Akhir Telah Dibuat',
                        'message' => 'Ujian tugas akhir Anda dinyatakan DITOLAK. Silakan hubungi Kaprodi untuk informasi lebih lanjut.',
                        'link' => route('mahasiswa.thesis.show', $thesis_exam->thesis->id),
                        'notes' => $validated['revisi_notes'],
                    ];
                    break;
            }
            $student->notify(new ThesisExamNotification($notificationData));
        }

        return redirect()->route('review.nilai.ujian.show', $thesis_exam->id)->with('success', 'Keputusan akhir ujian berhasil disimpan.');
    }


}
