<?php

namespace App\Http\Controllers\Nilai;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\GradeCorrectionRequest;
use App\Services\GradeCorrectionService;

class MhsRequestGradeCorrectionController extends Controller
{
    protected $service;

    public function __construct(GradeCorrectionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $userId = auth()->id();
        $ongoingRequests = GradeCorrectionRequest::where('user_id', $userId)
            ->whereIn('status', ['submitted', 'processing', 'validated', 'pending_kaprodi'])
            ->get();
        $historyRequests = GradeCorrectionRequest::where('user_id', $userId)
            ->whereIn('status', ['approved', 'rejected'])
            ->get();
        return view('remedial.mhs.index', compact('ongoingRequests', 'historyRequests'));
    }

    public function show(GradeCorrectionRequest $request)
    {
        if ($request->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }
        $courseSemester = $request->course->semester;

        $schedule = $request->course->schedules()
            ->orderBy('created_at', 'desc')
            ->first();

        $lecturers = $schedule ? $schedule->lecturersInSchedule : collect();
        return view('remedial.mhs.show', compact('request', 'lecturers'));
    }

    public function create()
    {
        $courses = Course::where('department_id', auth()->user()->student->department_id)->get();
        return view('remedial.mhs.create', compact('courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'current_grade' => 'required',
            'semester' => 'required',
        ]);

        $this->service->createRequest($data, auth()->user());
        return redirect()->route('mahasiswa.remedial.index')->with('success', 'Permintaan perbaikan nilai berhasil diajukan.');
    }

}