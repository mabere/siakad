<?php

namespace App\Http\Controllers\Nilai;

use App\Models\Course;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\GradeCorrectionRequest;
use App\Services\GradeCorrectionService;

class GradesCorrectionController extends Controller
{
    protected $gradeCorrectionService;

    public function __construct(GradeCorrectionService $gradeCorrectionService)
    {
        $this->gradeCorrectionService = $gradeCorrectionService;
    }

    public function index()
    {
        $user = auth()->user();
        $ongoingRequests = $this->gradeCorrectionService->getOngoingRequests($user);
        $historyRequests = $this->gradeCorrectionService->getHistoryRequests($user);
        $schedule = null;
        $lecturers = collect();
        if ($ongoingRequests->isNotEmpty() || $historyRequests->isNotEmpty()) {
            $firstRequest = $ongoingRequests->first() ?? $historyRequests->first();
            $schedule = $firstRequest->course->schedules()->orderBy('created_at', 'desc')->first();
            $lecturers = $schedule ? $schedule->lecturersInSchedule : collect();
        }

        return view('remedial.index', compact('ongoingRequests', 'historyRequests', 'lecturers'));
    }


    public function show(GradeCorrectionRequest $request)
    {
        $user = auth()->user();
        $data = $this->gradeCorrectionService->getRequestDetails($request, $user);
        return view('remedial.show', $data);
    }

    public function create()
    {
        $user = auth()->user();
        $student = $user->student;
        $activeAcademicYear = AcademicYear::active();

        // Ambil study plan mahasiswa yang disetujui di tahun akademik aktif
        $studyPlanScheduleIds = DB::table('study_plans')
            ->where('student_id', $student->id)
            ->where('academic_year_id', $activeAcademicYear->id)
            ->where('status', 'approved')
            ->pluck('schedule_id');

        // Nilai yang diperbolehkan untuk remedial
        $allowedGrades = ['K', 'T', 'E', 'D', 'C', '-', 'NULL'];

        // Ambil nilai huruf dari tabel grades yang sesuai dengan allowedGrades
        $grades = DB::table('grades')
            ->where('student_id', $student->id)
            ->where('academic_year_id', $activeAcademicYear->id)
            ->whereIn('schedule_id', $studyPlanScheduleIds)
            ->whereIn('nhuruf', $allowedGrades)
            ->pluck('nhuruf', 'schedule_id');

        // Filter schedules berdasarkan schedule_id yang ada di grades
        $schedules = DB::table('schedules')
            ->whereIn('id', array_keys($grades->toArray()))
            ->get();

        if ($schedules->isEmpty()) {
            return redirect()->route('mhs.remedial.index')->with('error', 'Tidak ada mata kuliah yang memenuhi syarat untuk remedial.');
        }

        $courseIds = $schedules->pluck('course_id');

        // Ambil course yang belum pernah diajukan remedial oleh mahasiswa ini
        $existingCourses = GradeCorrectionRequest::where('user_id', $user->id)
            ->where('status', '!=', 'rejected')
            ->pluck('course_id');

        $courses = Course::whereIn('id', $courseIds)
            ->whereNotIn('id', $existingCourses)
            ->get();

        if ($courses->isEmpty()) {
            return redirect()->route('mhs.remedial.index')->with('error', 'Semua mata kuliah yang memenuhi syarat sudah diajukan untuk perbaikan nilai.');
        }

        // Pemetaan course_id => schedule_id
        $scheduleMap = $schedules->pluck('id', 'course_id')->all();

        return view('remedial.create', compact('courses', 'grades', 'activeAcademicYear', 'scheduleMap'));
    }


    public function store(Request $request)
    {
        $user = auth()->user();
        $activeAcademicYear = AcademicYear::active();
        $departmentId = $user->student->department_id;

        $validated = $request->validate([
            'course_id' => [
                'required',
                'exists:courses,id',
                function ($attribute, $value, $fail) use ($activeAcademicYear, $departmentId, $user) {
                    // Validasi course_id ada di schedule aktif
                    $existsInSchedule = DB::table('schedules')
                        ->where('academic_year_id', $activeAcademicYear->id)
                        ->where('course_id', $value)
                        ->whereIn('course_id', function ($query) use ($departmentId) {
                        $query->select('id')
                            ->from('courses')
                            ->where('department_id', $departmentId);
                    })
                        ->exists();

                    if (!$existsInSchedule) {
                        $fail('Mata kuliah ini tidak tersedia pada semester berjalan.');
                    }

                    // Validasi tidak ada pengajuan aktif untuk course_id yang sama
                    $existingRequest = GradeCorrectionRequest::where('user_id', $user->id)
                        ->where('course_id', $value)
                        ->where('status', '!=', 'rejected')
                        ->exists();

                    if ($existingRequest) {
                        $fail('Anda sudah mengajukan perbaikan nilai untuk mata kuliah ini.');
                    }
                },
            ],
            'current_grade' => 'required|in:A,B,C,D,E',
            'semester' => 'required|integer|min:1|max:8',
            'document' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        // Validasi current_grade sesuai grades.nhuruf
        $scheduleId = DB::table('schedules')
            ->where('course_id', $validated['course_id'])
            ->where('academic_year_id', $activeAcademicYear->id)
            ->value('id');
        $actualGrade = DB::table('grades')
            ->where('student_id', $user->student->id)
            ->where('schedule_id', $scheduleId)
            ->value('nhuruf');

        if ($actualGrade !== $validated['current_grade']) {
            return redirect()->back()->withErrors(['current_grade' => 'Nilai saat ini tidak sesuai dengan data nilai Anda.']);
        }

        // if ($request->hasFile('document')) {
        //     $validated['document_path'] = $request->file('document')->store('documents', 'public');
        // }

        $this->gradeCorrectionService->createRequest($validated, $user);

        return redirect()->route('mhs.remedial.index')->with('success', 'Pengajuan perbaikan nilai berhasil diajukan.');
    }

    public function review(GradeCorrectionRequest $request, Request $httpRequest)
    {
        $user = auth()->user();
        $validated = $httpRequest->validate(['notes' => 'required|string|max:500']);
        $this->gradeCorrectionService->reviewByStaff($request, $user);
        return redirect()->route('staff.remedial.index')->with('success', 'Review berhasil disimpan.');
    }

    public function process(GradeCorrectionRequest $request, Request $httpRequest)
    {
        $user = auth()->user();
        \Log::info('Processing request by dosen', ['request_id' => $request->id, 'user_id' => $user->id, 'roles' => $user->roles->pluck('name')->toArray()]);

        try {
            $validated = $httpRequest->validate([
                'notes' => 'required|string|max:500',
                'current_grade' => 'required|string|in:A,B,C,D,E',
                'requested_grade' => 'required|string|in:A,B,C,D,E',
            ]);

            $this->gradeCorrectionService->processByDosen($request, $user, $validated);

            \Log::info('Dosen processing completed', ['request_id' => $request->id, 'status' => $request->status]);
            return redirect()->route('dosen.remedial.index')->with('success', 'Proses perbaikan nilai berhasil disimpan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed in dosen process', ['errors' => $e->errors(), 'request_id' => $request->id]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error in dosen process', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'request_id' => $request->id]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pengajuan.');
        }
    }

    public function performValidate(GradeCorrectionRequest $request, Request $httpRequest)
    {
        $user = auth()->user();
        $validated = $httpRequest->validate(['notes' => 'required|string|max:500']);
        $this->gradeCorrectionService->validateByStaff($request, $user);
        return redirect()->route('staff.remedial.index')->with('success', 'Validasi berhasil disimpan.');
    }

    public function approve(GradeCorrectionRequest $request, Request $httpRequest)
    {
        $user = auth()->user();
        $validated = $httpRequest->validate([
            'notes' => 'required|string|max:500',
            'approval_status' => 'required|in:approved,rejected',
        ]);
        $this->gradeCorrectionService->approveByKaprodi($request, $user);
        return redirect()->route('kaprodi.remedial.index')->with('success', 'Keputusan persetujuan berhasil disimpan.');
    }

    public function indexKaprodi()
    {
        $user = auth()->user();
        $departmentId = $user->lecturer->department_id;

        if (!$departmentId) {
            throw new \Exception("Department ID untuk kaprodi tidak ditemukan untuk user {$user->id}");
        }

        $validUserIds = DB::table('students')
            ->where('department_id', $departmentId)
            ->pluck('user_id');

        $ongoingRequests = GradeCorrectionRequest::whereIn('status', ['processing', 'pending_kaprodi'])
            ->whereIn('user_id', $validUserIds)
            ->get();
        $historyRequests = GradeCorrectionRequest::whereIn('status', ['approved', 'rejected'])
            ->whereIn('user_id', $validUserIds)
            ->get();

        $schedule = null;
        $lecturers = collect();
        if ($ongoingRequests->isNotEmpty() || $historyRequests->isNotEmpty()) {
            $firstRequest = $ongoingRequests->first() ?? $historyRequests->first();
            $schedule = $firstRequest->course->schedules()->orderBy('created_at', 'desc')->first();
            $lecturers = $schedule ? $schedule->lecturersInSchedule : collect();
        }

        return view('remedial.index', compact('ongoingRequests', 'historyRequests', 'lecturers'));
    }
}