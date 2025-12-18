<?php

namespace App\Http\Controllers\Nilai;

use App\Http\Controllers\Controller;
use App\Models\GradeCorrectionRequest;
use App\Services\GradeCorrectionService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class DosenProcessGradeCorrectionController extends Controller
{
    protected $service;

    public function __construct(GradeCorrectionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $lecturerId = auth()->user()->lecturer->id;

        // Pengajuan Berlangsung (status: processing)
        $ongoingRequests = GradeCorrectionRequest::whereHas('course.schedules', function ($query) use ($lecturerId) {
            $query->whereHas('lecturersInSchedule', function ($subQuery) use ($lecturerId) {
                $subQuery->where('lecturer_id', $lecturerId);
            });
        })
            ->where('status', 'processing')
            ->get();

        // Riwayat Pengajuan (status: validated, pending_kaprodi, approved, rejected)
        $historyRequests = GradeCorrectionRequest::whereHas('course.schedules', function ($query) use ($lecturerId) {
            $query->whereHas('lecturersInSchedule', function ($subQuery) use ($lecturerId) {
                $subQuery->where('lecturer_id', $lecturerId);
            });
        })
            ->whereIn('status', ['validated', 'pending_kaprodi', 'approved', 'rejected'])
            ->get();

        return view('remedial.dosen.index', compact('ongoingRequests', 'historyRequests'));
    }

    public function show(GradeCorrectionRequest $request)
    {
        $lecturerId = auth()->user()->lecturer->id;

        // Cek apakah dosen yang login termasuk dosen pengampu untuk pengajuan ini
        $isAuthorized = GradeCorrectionRequest::where('id', $request->id)
            ->whereHas('course.schedules', function ($query) use ($lecturerId) {
                $query->whereHas('lecturersInSchedule', function ($subQuery) use ($lecturerId) {
                    $subQuery->where('lecturer_id', $lecturerId);
                });
            })
            ->exists();

        if (!$isAuthorized) {
            \Log::warning('Unauthorized access to remedial show (dosen)', [
                'request_id' => $request->id,
                'lecturer_id' => $lecturerId,
            ]);
            abort(403, 'Unauthorized access.');
        }

        // Ambil semester dari course
        $courseSemester = $request->course->semester;

        // Ambil schedule terkait course (tanpa filter semester, ambil yang terbaru)
        $schedule = $request->course->schedules()
            ->orderBy('created_at', 'desc')
            ->first();

        // Ambil semua dosen pengampu dari schedule
        $lecturers = $schedule ? $schedule->lecturersInSchedule : collect();

        \Log::info('Dosen Remedial Show', [
            'request_id' => $request->id,
            'course_id' => $request->course_id,
            'course_semester' => $courseSemester,
            'request_semester' => $request->semester,
            'schedule_id' => $schedule ? $schedule->id : null,
            'lecturers' => $lecturers->pluck('name')->toArray(),
        ]);

        return view('remedial.dosen.show', compact('request', 'lecturers'));
    }

    public function process(Request $request, GradeCorrectionRequest $gradeCorrectionRequest): RedirectResponse
    {
        $lecturerId = auth()->user()->lecturer->id;

        if (
            !$gradeCorrectionRequest->course->schedules()->whereHas('lecturersInSchedule', function ($query) use ($lecturerId) {
                $query->where('lecturer_id', $lecturerId);
            })->exists()
        ) {
            abort(403, 'Unauthorized access.');
        }

        if ($gradeCorrectionRequest->status !== 'processing') {
            return redirect()->route('dosen.remedial.index')->with('error', 'Pengajuan ini tidak dalam status processing.');
        }

        $updatedData = $request->validate([
            'requested_grade' => 'required|string|max:2',
            'notes' => 'nullable|string|max:500',
        ]);

        $this->service->processByDosen($gradeCorrectionRequest, auth()->user(), $updatedData);
        return redirect()->route('dosen.remedial.index')->with('success', 'Perbaikan nilai berhasil diproses.');
    }
}