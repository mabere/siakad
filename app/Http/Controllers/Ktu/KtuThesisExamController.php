<?php

namespace App\Http\Controllers\Ktu;

use App\Models\User;
use App\Models\Thesis;
use App\Models\Lecturer;
use App\Models\ThesisExam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ThesisExamStatusLog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ThesisExamNotification;

class KtuThesisExamController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $ktuFacultyId = $user->employee->faculty_id;

        if (!$ktuFacultyId) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas KTU tidak ditemukan.');
        }
        $exams = ThesisExam::whereIn('status', ['diajukan', 'lulus', 'lulus_revisi', 'ditolak'])
            ->whereHas('thesis.student.department', function ($query) use ($ktuFacultyId) {
                $query->where('faculty_id', $ktuFacultyId);
            })
            ->with('thesis.student.user', 'thesis.student.department')
            ->latest('created_at')
            ->get();
        return view('backend.skripsi.ktu.index', compact('exams'));
    }

    public function show(ThesisExam $exam)
    {
        $exam->load([
            'thesis.student.user',
            'thesis.student.department',
            'documents'
        ]);
        return view('backend.skripsi.ktu.show', compact('exam'));
    }

    public function verify(Request $request, ThesisExam $exam)
    {
        $request->validate([
            'doc_statuses' => 'required|array',
            'doc_statuses.*' => 'in:pending,verifikasi,revisi',
            'doc_notes' => 'nullable|array',
            'doc_notes.*' => 'nullable|string|max:1000',
            'action' => 'required|in:revisi,setujui',
            'notes' => 'nullable|string|max:1000',
        ]);
        DB::beginTransaction();
        try {
            $statuses = $request->input('doc_statuses');
            $notes = $request->input('notes');
            $action = $request->input('action');
            $isApproved = $action === 'setujui';
            if ($isApproved) {
                if (in_array('revisi', $statuses) || in_array('pending', $statuses)) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Semua dokumen harus disetujui untuk melanjutkan verifikasi ujian.');
                }
                $newExamStatus = 'terverifikasi';
            } else {
                $newExamStatus = 'revisi';
            }
            foreach ($exam->documents as $doc) {
                if (isset($statuses[$doc->id])) {
                    $doc->update([
                        'status' => $statuses[$doc->id],
                        'notes' => $request->input('doc_notes')[$doc->id] ?? null,
                    ]);
                }
            }
            $oldStatus = $exam->status;
            $exam->update([
                'status' => $newExamStatus,
                'revisi_notes' => $notes,
            ]);
            ThesisExamStatusLog::create([
                'thesis_exam_id' => $exam->id,
                'old_status' => $oldStatus,
                'new_status' => $newExamStatus,
                'notes' => $notes,
                'changed_by' => auth()->id(),
                'changed_at' => now(),
            ]);
            if ($exam->thesis && $exam->thesis->student && $exam->thesis->student->user) {
                $exam->thesis->student->user->notify(new ThesisExamNotification([
                    'title' => $isApproved ? 'Verifikasi Berhasil' : 'Revisi Pengajuan Ujian',
                    'message' => $isApproved
                        ? 'Pengajuan ujian Anda telah diverifikasi oleh KTU. Tunggu jadwal ujian akan segera diumumkan.'
                        : 'Pengajuan ujian Anda ditolak dan memerlukan perbaikan dokumen. Silakan cek detailnya.',
                    'notes' => $notes,
                    'link' => route('student.thesis.exam.show', $exam->id),
                ]));
            }
            DB::commit();
            return redirect()->route('ktu.thesis.exam.index')->with('success', 'Verifikasi berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Verifikasi gagal: ' . $e->getMessage());
        }
    }

    // Manajemen Jadwal
    public function indexSchedule()
    {
        $exams = ThesisExam::where('status', ThesisExam::STATUS_DISETUJUI_DEKAN)
            ->orWhere('status', ThesisExam::STATUS_DIJADWALKAN)
            ->with(['thesis.student.user', 'thesis.supervisions.supervisor'])
            ->latest('updated_at')
            ->paginate(15);

        return view('backend.skripsi.ktu.index-schedule', compact('exams'));
    }

    public function showSchedule(Request $request, ThesisExam $exam)
    {
        $this->authorize('viewSchedule', $exam);
        $lecturers = Lecturer::with('user')->orderBy('nama_dosen')->get();
        $exam->load([
            'thesis.student.user',
            'thesis.supervisions.supervisor.user',
            'examiners.lecturer.user',
            'chairman.user',
            'secretary.user'
        ]);
        $supervisors = $exam->thesis->supervisions->pluck('supervisor_id')->toArray();
        return view('backend.skripsi.ktu.list-jadwal-ujian', compact('exam', 'lecturers', 'supervisors'));
    }

    // Metode penjadwalan ujian
    public function formSchedule(Request $request, ThesisExam $exam)
    {
        $this->authorize('viewSchedule', $exam);
        $lecturers = Lecturer::orderBy('nama_dosen')->get();
        return view('backend.skripsi.ktu.schedule-form', compact('exam', 'lecturers'));
    }

    public function storeSchedule(Request $request, ThesisExam $exam)
    {
        $this->authorize('scheduleExam', $exam);

        $request->validate([
            'scheduled_at' => 'required|date',
            'location' => 'required|string|max:255',
            'chairman_id' => 'required|exists:lecturers,id',
            'secretary_id' => 'required|exists:lecturers,id',
        ]);

        // Simpan status lama sebelum diperbarui
        $oldStatus = $exam->status;
        $newStatus = ThesisExam::STATUS_DIJADWALKAN;

        // Perbarui data ujian
        $exam->update([
            'scheduled_at' => $request->scheduled_at,
            'location' => $request->location,
            'status' => $newStatus,
            'chairman_id' => $request->chairman_id,
            'secretary_id' => $request->secretary_id,
        ]);

        // Log perubahan status
        ThesisExamStatusLog::create([
            'thesis_exam_id' => $exam->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => 'Jadwal ujian telah ditetapkan.',
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);

        // Notifikasi ke mahasiswa
        $exam->thesis->student->user->notify(new ThesisExamNotification([
            'title' => 'Jadwal Ujian Telah Ditetapkan',
            'message' => 'Jadwal ujian skripsi Anda telah ditentukan.',
            'link' => route('dashboard'),
        ]));

        // Notifikasi ke pembimbing
        foreach ($exam->thesis->supervisions as $supervision) {
            $supervision->supervisor->user->notify(new ThesisExamNotification([
                'title' => 'Jadwal Ujian Mahasiswa Bimbingan Anda',
                'message' => "Jadwal ujian skripsi untuk mahasiswa {$exam->thesis->student->nama_mhs} telah ditetapkan.",
                'link' => route('dashboard'),
            ]));
        }

        // Notifikasi ke dosen penguji
        foreach ($exam->examiners as $examiner) {
            $examiner->lecturer->user->notify(new ThesisExamNotification([
                'title' => 'Jadwal Ujian Skripsi Mahasiswa',
                'message' => "Anda dijadwalkan menjadi penguji skripsi mahasiswa {$exam->thesis->student->nama_mhs}.",
                'link' => route('dashboard'),
            ]));
        }

        // Notifikasi ke role internal kampus
        $rolesToNotify = ['ktu', 'staff', 'kaprodi', 'dekan', 'admin'];

        $users = User::whereHas('roles', fn($q) => $q->whereIn('name', $rolesToNotify))->get();

        foreach ($users as $user) {
            $user->notify(new ThesisExamNotification([
                'title' => 'Penjadwalan Ujian Skripsi',
                'message' => "Jadwal ujian skripsi mahasiswa {$exam->thesis->student->nama_mhs} telah ditetapkan.",
                'link' => route('dashboard'),
            ]));
        }

        return redirect()->route('ktu.thesis.schedule.show', $exam->id)->with('success', 'Jadwal ujian berhasil disimpan.');
    }


    public function updateReschedule(Request $request, ThesisExam $exam)
    {
        // Otorisasi menggunakan model ThesisExam yang benar
        $this->authorize('reschedule', $exam);

        $request->validate([
            'scheduled_at' => 'required|date',
            'location' => 'required|string|max:255',
            'chairman_id' => 'required|exists:lecturers,id',
            'secretary_id' => 'required|exists:lecturers,id',
        ]);

        // Simpan status lama sebelum diperbarui
        $oldStatus = $exam->status;
        $newStatus = ThesisExam::STATUS_DIJADWALKAN;

        $exam->update([
            'scheduled_at' => $request->scheduled_at,
            'location' => $request->location,
            'chairman_id' => $request->chairman_id,
            'secretary_id' => $request->secretary_id,
        ]);

        // Log perubahan jadwal
        ThesisExamStatusLog::create([
            'thesis_exam_id' => $exam->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => 'Jadwal ujian diubah oleh KTU.',
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);

        // Notifikasi ke mahasiswa
        $exam->thesis->student->user->notify(new ThesisExamNotification([
            'title' => 'Perubahan Jadwal Ujian',
            'message' => 'Jadwal ujian skripsi Anda telah diubah. Silakan cek detail terbaru.',
            'link' => route('ktu.thesis.schedule.show', $exam->id),
        ]));

        // Notifikasi ke pembimbing
        foreach ($exam->thesis->supervisions as $supervision) {
            $supervision->supervisor->user->notify(new ThesisExamNotification([
                'title' => 'Perubahan Jadwal Ujian Mahasiswa Bimbingan Anda',
                'message' => "Jadwal ujian skripsi untuk mahasiswa {$exam->thesis->student->nama_mhs} telah diubah.",
                'link' => route('ktu.thesis.schedule.show', $exam->id),
            ]));
        }

        // Notifikasi ke dosen penguji
        foreach ($exam->examiners as $examiner) {
            $examiner->lecturer->user->notify(new ThesisExamNotification([
                'title' => 'Perubahan Jadwal Ujian Skripsi',
                'message' => "Jadwal ujian skripsi mahasiswa {$exam->thesis->student->nama_mhs} telah diubah.",
                'link' => route('ktu.thesis.schedule.show', $exam->id),
            ]));
        }

        return redirect()->route('ktu.thesis.schedule.show', $exam->id)->with('success', 'Jadwal ujian berhasil diperbarui.');
    }

}