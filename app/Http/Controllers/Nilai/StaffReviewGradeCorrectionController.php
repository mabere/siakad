<?php

namespace App\Http\Controllers\Nilai;

use App\Http\Controllers\Controller;
use App\Models\GradeCorrectionRequest;
use App\Services\GradeCorrectionService;
use Illuminate\Http\Request;

class StaffReviewGradeCorrectionController extends Controller
{
    protected $service;

    public function __construct(GradeCorrectionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $requests = GradeCorrectionRequest::whereHas('user.student.department', function ($query) {
            $query->where('id', auth()->user()->employee->department_id);
        })->get();

        return view('remedial.staff.index', compact('requests'));
    }

    public function shows(GradeCorrectionRequest $request)
    {
        // Pastikan staff hanya bisa melihat pengajuan dari departemennya
        if ($request->user->student->department_id !== auth()->user()->employee->department_id) {
            abort(403, 'Unauthorized access.');
        }

        return view('remedial.staff.show', compact('request'));
    }
    public function show(GradeCorrectionRequest $request)
    {
        // Pastikan staff hanya bisa melihat pengajuan dari departemennya
        if ($request->user->student->department_id !== auth()->user()->employee->department_id) {
            abort(403, 'Unauthorized access.');
        }
        $schedule = $request->course->schedules()
            ->orderBy('created_at', 'desc')
            ->first();

        // Ambil semua dosen pengampu dari schedule
        $lecturers = $schedule ? $schedule->lecturersInSchedule : collect();
        return view('remedial.staff.show', compact('request', 'lecturers')); // Perbaiki path view menjadi 'staff.remedial.show'
    }

    public function review(GradeCorrectionRequest $request)
    {
        // Pastikan staff hanya bisa mereview pengajuan dari departemennya
        if ($request->user->student->department_id !== auth()->user()->employee->department_id) {
            abort(403, 'Unauthorized access.');
        }

        // Pastikan status masih submitted
        if ($request->status !== 'submitted') {
            return redirect()->route('staff.remedial.index')->with('error', 'Pengajuan ini sudah direview.');
        }

        $this->service->reviewByStaff($request, auth()->user());
        return redirect()->route('staff.remedial.index')->with('success', 'Pengajuan berhasil direview.');
    }

    public function staffValidate(GradeCorrectionRequest $request)
    {
        if ($request->user->student->department_id !== auth()->user()->employee->department_id) {
            abort(403, 'Unauthorized access.');
        }

        if ($request->status !== 'validated') {
            return redirect()->route('staff.remedial.index')->with('error', 'Pengajuan ini belum divalidasi.');
        }

        $this->service->validateByStaff($request, auth()->user());
        return redirect()->route('staff.remedial.index')->with('success', 'Nilai berhasil divalidasi.');
    }
}