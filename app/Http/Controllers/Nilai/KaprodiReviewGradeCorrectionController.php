<?php

namespace App\Http\Controllers\Nilai;

use App\Http\Controllers\Controller;
use App\Models\GradeCorrectionRequest;
use App\Services\GradeCorrectionService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class KaprodiReviewGradeCorrectionController extends Controller
{
    protected $service;

    public function __construct(GradeCorrectionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $requests = GradeCorrectionRequest::whereHas('user.student.department', function ($query) {
            $query->where('id', auth()->user()->lecturer->department_id);
        })->get();
        return view('remedial.kaprodi.index', compact('requests'));
    }

    public function approve(GradeCorrectionRequest $request): RedirectResponse
    {
        $this->service->approveByKaprodi($request, auth()->user());
        return redirect()->route('kaprodi.remedial.index')->with('success', 'Pengajuan berhasil disetujui.');
    }

    public function show(GradeCorrectionRequest $request)
    {
        if ($request->user->student->department_id !== auth()->user()->lecturer->department_id) {
            abort(403, 'Unauthorized access.');
        }
        $schedule = $request->course->schedules()
        ->orderBy('created_at', 'desc')
        ->first();

    $lecturers = $schedule ? $schedule->lecturersInSchedule : collect();
        return view('remedial.kaprodi.show', compact('request','lecturers'));
    }


}