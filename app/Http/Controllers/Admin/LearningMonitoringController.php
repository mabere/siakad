<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LearningMonitoring;
use App\Models\Department;
use App\Models\AcademicYear;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\MonitoringRequest;

class LearningMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $query = LearningMonitoring::with(['schedule.schedulable', 'schedule.lecturersInSchedule', 'monitor'])
            ->when($request->department_id, function ($q) use ($request) {
                return $q->where('department_id', $request->department_id);
            })
            ->when($request->academic_year_id, function ($q) use ($request) {
                return $q->where('academic_year_id', $request->academic_year_id);
            });

        $monitorings = $query->latest()->paginate(10);

        $departments = Department::all();
        $academicYears = AcademicYear::orderBy('ta', 'desc')->get();

        return view('admin.monitoring.index', compact(
            'monitorings',
            'departments',
            'academicYears'
        ));
    }

    // public function create()
    // {
    //     $schedules = Schedule::with(['course', 'lecturersInSchedule'])
    //         ->where('academic_year_id', getCurrentAcademicYear()->id)
    //         ->get();

    //     return view('admin.monitoring.create', compact('schedules'));
    // }

    public function store(MonitoringRequest $request)
    {
        DB::transaction(function () use ($request) {
            $schedule = Schedule::findOrFail($request->schedule_id);

            $monitoring = LearningMonitoring::create([
                'schedule_id' => $request->schedule_id,
                'academic_year_id' => getCurrentAcademicYear()->id,
                'department_id' => $schedule->course->department_id,
                'monitor_id' => auth()->id(),
                'meeting_number' => $request->meeting_number,
                'monitoring_date' => $request->monitoring_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'attendance_count' => $request->attendance_count,
                'material_conformity' => $request->material_conformity,
                'learning_method' => $request->learning_method,
                'media_used' => $request->media_used,
                'notes' => $request->notes,
                'status' => 'submitted'
            ]);

            if ($request->has('aspects')) {
                foreach ($request->aspects as $key => $aspect) {
                    if (isset($aspect['score'])) {
                        $monitoring->aspects()->create([
                            'aspect_name' => $key,
                            'score' => $aspect['score'],
                            'notes' => $aspect['notes'] ?? null
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route('admin.monitoring.index')
            ->with('success', 'Data monitoring berhasil disimpan');
    }

    public function show(LearningMonitoring $monitoring)
    {
        $monitoring->load(['schedule.course', 'schedule.lecturersInSchedule', 'aspects']);

        return view('admin.monitoring.show', compact('monitoring'));
    }

    public function verify(LearningMonitoring $monitoring, Request $request)
    {
        $monitoring->update([
            'status' => 'verified',
            'verification_notes' => $request->verification_notes,
            'verified_at' => now(),
            'verified_by' => auth()->id()
        ]);

        return redirect()
            ->route('admin.monitoring.show', $monitoring)
            ->with('success', 'Monitoring berhasil diverifikasi');
    }

    public function requestRevision(LearningMonitoring $monitoring, Request $request)
    {
        $request->validate([
            'revision_notes' => 'required|string'
        ]);

        $monitoring->update([
            'status' => 'revised',
            'revision_notes' => $request->revision_notes
        ]);

        return redirect()
            ->route('admin.monitoring.show', $monitoring)
            ->with('success', 'Permintaan revisi berhasil dikirim');
    }

}