<?php

namespace App\Http\Controllers\Dekan;

use App\Models\Faculty;
use App\Models\Kegiatan;
use App\Models\Department;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreEvenRequest;
use App\Http\Requests\UpdateEvenRequest;

class KegiatanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkRole:dekan|kaprodi')->only(['index', 'create', 'store']);
    }

    public function index()
    {
        $user = Auth::user();
        $userRole = $user->roles->first()?->name;
        $facultyId = $user->lecturer?->department?->faculty_id;
        $departmentId = $user->lecturer?->department?->id;
        if (!$facultyId) {
            Log::warning("User has no faculty association.", ['user_id' => $user->id, 'role' => $userRole]);
            $events = collect();
        } else {
            $query = Kegiatan::query()->where('created_by', $user->id)
                ->orWhere(function ($q) use ($facultyId, $departmentId, $userRole) {
                    $q->where('visibility', 'faculty')
                        ->where('faculty_id', $facultyId);
                    if ($userRole === 'kaprodi' && $departmentId) {
                        $q->orWhere(function ($subQ) use ($departmentId) {
                            $subQ->where('visibility', 'department')
                                ->where('department_id', $departmentId);
                        });
                    } elseif ($userRole === 'dekan') {
                        $q->orWhere(function ($subQ) use ($facultyId) {
                            $subQ->where('visibility', 'department')
                                ->whereHas('department', function ($depQuery) use ($facultyId) {
                                    $depQuery->where('faculty_id', $facultyId);
                                });
                        });
                    }
                })
                ->whereIn('status', ['draft', 'published'])
                ->where(function ($audienceQuery) {
                    $audienceQuery->where('target_audience', 'semua')
                        ->orWhere('target_audience', 'dosen');
                });
            $events = $query->with(['faculty', 'department', 'academicYear'])->get();
        }
        $faculties = Faculty::all();
        $academicYears = AcademicYear::all();
        $departments = Department::where('faculty_id', $facultyId)->get();
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
        return view('dekan.kegiatan.index', compact('events', 'eventsData', 'faculties', 'departments', 'academicYears'));
    }

    public function create()
    {
        $user = Auth::user();
        $facultyId = $user->lecturer?->department?->faculty_id;
        if (!$facultyId || !Faculty::where('id', $facultyId)->exists()) {
            abort(400, 'Pengguna tidak terkait dengan fakultas yang valid untuk membuat kegiatan.');
        }
        $faculties = Faculty::all();
        $academicYears = AcademicYear::all();
        $departments = Department::where('faculty_id', $facultyId)->get();
        return view('dekan.kegiatan.create', compact('faculties', 'departments', 'academicYears'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $facultyId = $user->lecturer?->department?->faculty_id;
        if (!$facultyId || !Faculty::where('id', $facultyId)->exists()) {
            Log::error('Invalid or missing faculty_id for user trying to create event', [
                'user_id' => $user->id,
                'faculty_id' => $facultyId
            ]);
            return redirect()->back()->with('error', 'Gagal membuat acara: Pengguna tidak terkait dengan fakultas yang valid.')->withInput();
        }
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'start_date' => 'required|date_format:Y-m-d\TH:i|before_or_equal:end_date',
                'end_date' => 'required|date_format:Y-m-d\TH:i',
                'description' => 'nullable|string',
                'url' => 'nullable|url|max:255',
                'visibility' => 'required|in:faculty,department',
                'department_id' => 'nullable|exists:departments,id|required_if:visibility,department',
                'academic_year_id' => 'required|exists:academic_years,id',
                'target_audience' => 'required|in:semua,mahasiswa,dosen', // Added validation for target_audience
            ]);

            if ($validated['visibility'] === 'department' && $validated['department_id']) {
                $department = Department::find($validated['department_id']);
                if (!$department || $department->faculty_id !== $facultyId) {
                    Log::warning('Attempt to create department event for a department not in user\'s faculty', [
                        'user_id' => $user->id,
                        'user_faculty_id' => $facultyId,
                        'requested_department_id' => $validated['department_id']
                    ]);
                    return redirect()->back()->with('error', 'Program studi yang dipilih tidak valid untuk fakultas Anda.')->withInput();
                }
            }
            $event = Kegiatan::create([
                'title' => $validated['title'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'description' => $validated['description'],
                'url' => $validated['url'] ?? null,
                'visibility' => $validated['visibility'],
                'target_audience' => $validated['target_audience'],
                'faculty_id' => $facultyId,
                'department_id' => $validated['visibility'] === 'department' ? $validated['department_id'] : null,
                'academic_year_id' => $validated['academic_year_id'],
                'created_by' => $user->id,
                'status' => 'draft',
            ]);

            Log::info('Event Created Successfully by Dekan/Kaprodi:', [
                'event_id' => $event->id,
                'event_data' => $event->toArray()
            ]);

            return redirect()->route('dekan.kegiatan.index')->with('success', 'Acara fakultas/prodi berhasil dibuat dan menunggu persetujuan.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error creating event by Dekan/Kaprodi', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create event by Dekan/Kaprodi', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Gagal membuat acara: ' . $e->getMessage())->withInput();
        }
    }

    public function publishEvent(Kegiatan $kegiatan)
    {
        if ($kegiatan->status === 'published') {
            return response()->json(['message' => 'Kegiatan sudah dipublikasikan.'], 200);
        }

        if ($kegiatan->status === 'cancelled') {
            return response()->json(['error' => 'Kegiatan telah dibatalkan dan tidak dapat dipublikasikan.'], 400);
        }

        try {
            $kegiatan->status = 'published';
            $kegiatan->save();

            Log::info('Kegiatan published successfully', ['event_id' => $kegiatan->id, 'published_by' => Auth::id()]);
            return redirect()->route('dekan.kegiatan.akademik.index')->with('success', 'Kegiatan berhasil dipublikasikan.');
        } catch (\Exception $e) {
            Log::error('Failed to publish kegiatan', [
                'event_id' => $kegiatan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('dekan.kegiatan.akademik.index')->with('error', 'Kegiatan gagal dipublikasikan.');
        }
    }

}
