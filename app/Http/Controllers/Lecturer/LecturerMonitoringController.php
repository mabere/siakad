<?php

namespace App\Http\Controllers\Lecturer;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\LearningMonitoring;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\MonitoringRequest;

class LecturerMonitoringController extends Controller
{
    public function index()
    {
        $schedules = Schedule::whereHas('lecturersInSchedule', function ($query) {
            $query->where('lecturers.id', auth()->user()->lecturer->id);
        })
            ->with(['course', 'kelas', 'monitorings'])
            ->get();

        $monitorings = LearningMonitoring::whereHas('schedule.lecturersInSchedule', function ($query) {
            $query->where('lecturers.id', auth()->user()->lecturer->id);
        })
            ->with(['schedule.course'])
            ->latest()
            ->paginate(10);

        return view('dosen.monitoring.index', compact('schedules', 'monitorings'));
    }

    public function show(LearningMonitoring $monitoring)
    {
        // Validasi kepemilikan
        if (!$this->isOwner($monitoring)) {
            abort(403, 'Anda tidak memiliki izin untuk melihat monitoring ini.');
        }

        // Load relasi yang diperlukan
        $monitoring->load(['schedule.course', 'schedule.kelas', 'schedule.academicYear', 'schedule.department']);

        return view('dosen.monitoring.show', compact('monitoring'));
    }

    public function create(Schedule $schedule)
    {
        $lecturer = auth()->user()->lecturer;
        $meetingRange = $lecturer->getMeetingRange($schedule);

        return view('dosen.monitoring.create', compact('schedule', 'meetingRange'));
    }

    public function store(Request $request)
    {
        $lecturer = auth()->user()->lecturer;
        $schedule = Schedule::findOrFail($request->schedule_id);
        $meetingRange = $lecturer->getMeetingRange($schedule);

        $existingMonitoring = LearningMonitoring::where('schedule_id', $schedule->id)
            ->where('meeting_number', $request->meeting_number)
            ->exists();

        if ($existingMonitoring) {
            return back()
                ->withInput()
                ->withErrors(['meeting_number' => "Monitoring untuk pertemuan ke-{$request->meeting_number} sudah dibuat."]);
        }

        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'meeting_number' => [
                'required',
                'integer',
                "min:{$meetingRange['start']}",
                "max:{$meetingRange['end']}",
                Rule::unique('learning_monitorings')
                    ->where(function ($query) use ($request) {
                        return $query->where('schedule_id', $request->schedule_id);
                    })
            ],
            'monitoring_date' => 'required|date',
            'attendance_count' => 'required|integer|min:0',
            'material_conformity' => 'required|boolean',
            'learning_method' => 'required|string',
            'media_used' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        // Mengganti logika lama dengan mengambil langsung dari kolom yang baru
        $start_time = $schedule->start_time;
        $end_time = $schedule->end_time;

        // Set nilai default dan tambahkan data dari schedule dan user
        $validated['status'] = 'submitted';
        $validated['academic_year_id'] = $schedule->academic_year_id;
        $validated['department_id'] = $schedule->department_id;
        $validated['monitor_id'] = auth()->id();
        $validated['start_time'] = $start_time;
        $validated['end_time'] = $end_time;

        $monitoring = LearningMonitoring::create($validated);

        return redirect()
            ->route('lecturer.monitoring.index')
            ->with('success', 'Monitoring berhasil ditambahkan');
    }

    public function edit(LearningMonitoring $monitoring)
    {
        $lecturer = auth()->user()->lecturer;

        if (!$lecturer) {
            Log::error('Lecturer not found for user', ['user_id' => auth()->id()]);
            abort(403, 'Data dosen tidak ditemukan.');
        }

        $schedule = $monitoring->schedule;
        if (!$schedule) {
            Log::error('Schedule not found for monitoring', ['monitoring_id' => $monitoring->id]);
            abort(404, 'Jadwal tidak ditemukan.');
        }

        if ($monitoring->status !== 'revised' || !$this->isOwner($monitoring)) {
            Log::warning('Edit access denied', [
                'monitoring_id' => $monitoring->id,
                'reason' => $monitoring->status !== 'revised' ? 'Status is not revised' : 'Not the owner',
            ]);
            abort(403, 'Anda tidak memiliki izin untuk mengedit monitoring ini.');
        }

        $meetingRange = $lecturer->getMeetingRange($schedule);

        return view('dosen.monitoring.edit', compact('monitoring', 'schedule', 'meetingRange'));
    }

    public function update(Request $request, LearningMonitoring $monitoring)
    {
        $lecturer = auth()->user()->lecturer;

        if (!$lecturer) {
            Log::error('Lecturer not found for user', ['user_id' => auth()->id()]);
            abort(403, 'Data dosen tidak ditemukan.');
        }

        $schedule = $monitoring->schedule;
        if (!$schedule) {
            Log::error('Schedule not found for monitoring', ['monitoring_id' => $monitoring->id]);
            abort(404, 'Jadwal tidak ditemukan.');
        }

        // Pastikan dosen adalah pemilik monitoring atau ada di jadwal
        if ($monitoring->status !== 'revised' || !$this->isOwner($monitoring)) {
            Log::warning('Update access denied', [
                'monitoring_id' => $monitoring->id,
                'reason' => $monitoring->status !== 'revised' ? 'Status is not revised' : 'Not the owner',
            ]);
            abort(403, 'Anda tidak memiliki izin untuk mengedit monitoring ini.');
        }

        $meetingRange = $lecturer->getMeetingRange($schedule);

        // Validasi data
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'meeting_number' => [
                'required',
                'integer',
                "min:{$meetingRange['start']}",
                "max:{$meetingRange['end']}",
                Rule::unique('learning_monitorings')
                    ->ignore($monitoring->id) // Menambahkan ignore untuk entri saat ini
                    ->where(function ($query) use ($request) {
                        return $query->where('schedule_id', $request->schedule_id);
                    })
            ],
            'monitoring_date' => 'required|date',
            'attendance_count' => 'required|integer|min:0',
            'material_conformity' => 'required|boolean',
            'learning_method' => 'required|string',
            'media_used' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        // Mengganti logika lama dengan mengambil langsung dari kolom yang baru
        $validated['start_time'] = $schedule->start_time;
        $validated['end_time'] = $schedule->end_time;

        // Update data tambahan
        $validated['status'] = 'submitted'; // Kembali ke submitted setelah edit
        $validated['academic_year_id'] = $schedule->academic_year_id;
        $validated['department_id'] = $schedule->department_id;
        $validated['monitor_id'] = auth()->id();

        $monitoring->update($validated);

        return redirect()
            ->route('lecturer.monitoring.index')
            ->with('success', 'Monitoring berhasil diperbarui');
    }

    private function isOwner(LearningMonitoring $monitoring)
    {
        return $monitoring->schedule->lecturersInSchedule->contains(auth()->user()->lecturer->id);
    }
}