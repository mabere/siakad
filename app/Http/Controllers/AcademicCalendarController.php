<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\AcademicYear;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AcademicCalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkRole:admin')->only(['indexAdmin', 'create', 'update', 'delete', 'publish', 'store', 'unpublish']);
    }

    // All Roles: View Calendar
    public function index()
    {
        $user = auth()->user();
        $faculties = Faculty::all();
        $departments = Department::all();
        $academicYears = AcademicYear::all();
        $events = Kegiatan::forUser($user)
            ->with(['faculty', 'department', 'academicYear'])
            ->get();
        $eventsData = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => htmlspecialchars($event->title, ENT_QUOTES, 'UTF-8'),
                'start' => $event->start_date->toIso8601String(),
                'end' => $event->end_date->toIso8601String(),
                'description' => htmlspecialchars($event->description ?? '', ENT_QUOTES, 'UTF-8'),
                'status' => $event->status,
                'visibility' => $event->visibility,
                'target_audience' => $event->target_audience,
                'faculty_id' => $event->faculty?->id,
                'faculty_name' => $event->faculty?->nama,
                'department_id' => $event->department?->id,
                'department_name' => $event->department?->nama,
                'academic_year_id' => $event->academicYear?->id,
                'url' => htmlspecialchars($event->url ?? '', ENT_QUOTES, 'UTF-8'),
            ];
        });
        return view('calendar.index', compact('events', 'eventsData', 'faculties', 'departments', 'academicYears'));
    }

    public function indexAdmin()
    {
        $events = Kegiatan::with(['faculty', 'department', 'academicYear', 'createdBy'])->get();
        $faculties = Faculty::all();
        $departments = Department::all();
        $academicYears = AcademicYear::all();
        return view('calendar.data', compact('events', 'faculties', 'departments', 'academicYears'));
    }

    public function store(Request $request)
    {
        Log::info('Reached createEvent method', ['user_id' => auth()->id(), 'request' => $request->all()]);

        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            Log::warning('Unauthorized access to createEvent', ['user_id' => $user->id]);
            return response()->json(['error' => 'Akses ditolak. Hanya admin yang dapat membuat acara.'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date_format:Y-m-d\TH:i:s|before_or_equal:end_date',
            'end_date' => 'required|date_format:Y-m-d\TH:i:s',
            'description' => 'nullable|string',
            'visibility' => 'required|in:public,faculty,department',
            'faculty_id' => 'nullable|exists:faculties,id|required_if:visibility,faculty',
            'department_id' => 'nullable|exists:departments,id|required_if:visibility,department',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        try {
            DB::enableQueryLog();

            if ($validated['visibility'] === 'faculty' && !Faculty::find($validated['faculty_id'])) {
                throw new \Exception('Faculty ID tidak valid.');
            }
            if ($validated['visibility'] === 'department' && !Department::find($validated['department_id'])) {
                throw new \Exception('Department ID tidak valid.');
            }
            if (!AcademicYear::find($validated['academic_year_id'])) {
                throw new \Exception('Academic Year ID tidak valid.');
            }

            Log::info('Attempting to create admin event with data:', [
                'title' => $validated['title'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'description' => $validated['description'],
                'visibility' => $validated['visibility'],
                'faculty_id' => $validated['faculty_id'],
                'department_id' => $validated['department_id'],
                'academic_year_id' => $validated['academic_year_id'],
                'created_by' => $user->id,
                'status' => 'draft'
            ]);

            $event = Kegiatan::create([
                'title' => $validated['title'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'description' => $validated['description'],
                'visibility' => $validated['visibility'],
                'faculty_id' => $validated['faculty_id'],
                'department_id' => $validated['department_id'],
                'academic_year_id' => $validated['academic_year_id'],
                'created_by' => $user->id,
                'status' => 'draft',
            ]);

            Log::info('Admin Event Created Successfully:', [
                'event_id' => $event->id,
                'event_data' => $event->toArray()
            ]);

            Log::info('Query Log:', DB::getQueryLog());
            DB::disableQueryLog();

            $savedEvent = Kegiatan::find($event->id);
            if (!$savedEvent) {
                Log::error('Event not found in database after creation', ['event_id' => $event->id]);
                throw new \Exception('Event gagal disimpan ke database.');
            }

            return response()->json([
                'success' => 'Acara berhasil dibuat sebagai draft.',
                'event_id' => $event->id,
                'event_data' => $event->toArray()
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to create admin event', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Gagal membuat acara: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Kegiatan $event)
    {
        $user = auth()->user();
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'visibility' => 'required|in:public,faculty,department',
            'target_audience' => 'nullable',
            'url' => 'nullable|url',
            'status' => 'required|in:draft,published,cancelled',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);
        if ($request->input('visibility') === 'faculty') {
            $request->validate(['faculty_id' => 'required|exists:faculties,id']);
            $validatedData['department_id'] = null;
        } elseif ($request->input('visibility') === 'department') {
            $request->validate(['department_id' => 'required|exists:departments,id']);
            $validatedData['faculty_id'] = null;
        } else {
            $validatedData['faculty_id'] = null;
            $validatedData['department_id'] = null;
        }

        if (!$user->hasRole('admin')) {
            if ($event->visibility === 'faculty' && $event->faculty_id !== $user->faculty_id) {
                return redirect()->route('kegiatan.index')->with('error', 'Akses ditolak. Anda tidak dapat mengedit kegiatan ini.');
            }
            if ($event->visibility === 'department' && $event->department_id !== $user->department_id) {
                return redirect()->route('kegiatan.index')->with('error', 'Akses ditolak. Anda tidak dapat mengedit kegiatan ini.');
            }
        }
        $event->update($validatedData);
        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function publish(Kegiatan $event)
    {
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            return redirect()->route('calendar.index')->with('error', 'Akses ditolak.');
        }
        $event->update(['status' => 'published']);
        return redirect()->route('kegiatan.index')->with('success', 'Acara berhasil dipublikasi.');
    }

    public function unpublish(Kegiatan $event)
    {
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            return redirect()->route('calendar.index')->with('error', 'Akses ditolak.');
        }
        $event->update(['status' => 'draft']);
        return redirect()->route('kegiatan.index')->with('success', 'Acara berhasil dikembalikan ke draft.');
    }

    public function destroy(Kegiatan $event)
    {
        $user = auth()->user();

        if (!$user->hasRole('admin')) {
            return redirect()->route('calendar.index')->with('error', 'Akses ditolak. Hanya admin yang dapat membatalkan acara.');
        }
        $event->update(['status' => 'cancelled']);
        return redirect()->route('kegiatan.index')->with('success', 'Acara berhasil dibatalkan.');
    }

    // Helper: Get relevant users for notification
    private function getRelevantUsers(Kegiatan $event)
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return User::all();
        }
        return User::whereHas('faculty', function ($q) use ($user) {
            $q->where('id', $user->faculty_id);
        })->get();
    }

}