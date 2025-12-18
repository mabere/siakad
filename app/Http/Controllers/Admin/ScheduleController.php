<?php

namespace App\Http\Controllers\Admin;

use App\Models\Room;
use App\Models\Kelas;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Schedule;
use App\Models\Department;
use App\Models\MkduCourse;
use Illuminate\Http\Request;
use App\Traits\ScheduleTrait;
use App\Services\ScheduleService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SchedulesImport;
use Illuminate\Validation\ValidationException;

class ScheduleController extends Controller
{
    use ScheduleTrait;

    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
        $this->middleware('auth');
    }

    public function index()
    {
        $ta = getCurrentAcademicYear();
        $prodi = $this->getVisibleDepartments();

        return view('backend.schedule.index', compact('ta', 'prodi'));
    }

    private function getVisibleDepartments()
    {
        if ($this->user()->hasRole('admin')) {
            return Department::with('faculty')->get();
        }

        return Department::with('faculty')
            ->where('id', $this->user()->employee->department_id)
            ->get();
    }

    public function show(Department $department)
    {
        $ta = getCurrentAcademicYear();
        if (!$ta) {
            Log::error('ScheduleController::show - No active academic year found');
            return back()->with('error', 'Tidak ada tahun akademik aktif.');
        }

        $this->authorize('view', [Schedule::class, $department]);

        $schedules = Schedule::where('department_id', $department->id)
            ->where('academic_year_id', $ta->id)
            ->with(['schedulable', 'lecturers', 'room', 'kelas'])
            ->get();

        return view('backend.schedule.show', compact('schedules', 'department', 'ta'));
    }

    public function getCoursesByType(Request $request)
    {
        $type = $request->query('type');
        $departmentId = $request->query('department_id');
        $semesterNumbers = [];
        $ta = getCurrentAcademicYear();
        if ($ta) {
            $semesterNumbers = $ta->semester === 'Ganjil' ? [1, 3, 5, 7] : [2, 4, 6, 8];
        }
        if ($type === 'prodi' && $departmentId) {
            $courses = Course::where('department_id', $departmentId)
                ->whereIn('semester_number', $semesterNumbers)
                ->orderBy('semester_number')
                ->get(['id', 'code', 'name', 'sks', 'semester_number']);
            return response()->json($courses);
        } elseif ($type === 'mkdu') {
            $mkduCourses = MkduCourse::whereIn('semester_number', $semesterNumbers)
                ->get(['id', 'code', 'name', 'sks', 'semester_number']);
            return response()->json($mkduCourses);
        }
        return response()->json([], 400);
    }

    public function create(Department $department)
    {
        $this->authorize('create', Schedule::class);

        $ta = getCurrentAcademicYear();
        if (!$ta) {
            Log::error('ScheduleController::create - No active academic year found');
            return back()->with('error', 'Tidak ada tahun akademik aktif.');
        }

        $semesterNumbers = $ta->semester === 'Ganjil' ? [1, 3, 5, 7] : [2, 4, 6, 8];

        $courses = Course::where('department_id', $department->id) // Gunakan $department->id dari parameter
            ->whereIn('semester_number', $semesterNumbers)
            ->orderBy('semester_number')
            ->get();
        $mkduCourses = MkduCourse::whereIn('semester_number', $semesterNumbers)->get();

        $rooms = Room::all();
        $kelas = Kelas::where('department_id', $department->id)->get(); // Gunakan $department->id
        // $lecturers = Lecturer::where('department_id', $department->id)->get(); // Gunakan $department->id khusus dosen prodi saja
        $lecturers = Lecturer::orderBy('department_id')->get(); // Mengakomodir dosen luar prodi
        // Inisialisasi variabel untuk dosen dan minggu pertemuan ke null
        // Ini memastikan variabel ini selalu ada di view, bahkan saat membuat baru
        $lecturer1_id = null;
        $lecturer1_start = null;
        $lecturer1_end = null;
        $lecturer2_id = null;
        $lecturer2_start = null;
        $lecturer2_end = null;

        return view('backend.schedule.create', compact(
            'department', // Pastikan ini juga dilewatkan ke view
            'courses',
            'mkduCourses',
            'rooms',
            'lecturers',
            'kelas',
            'ta',
            'lecturer1_id',
            'lecturer1_start',
            'lecturer1_end',
            'lecturer2_id',
            'lecturer2_start',
            'lecturer2_end'
        ));
    }

    public function store(Request $request, Department $department)
    {
        Log::debug('Entering store() method for department: ' . $department->id);

        // Dapatkan tahun akademik aktif
        $ta = getCurrentAcademicYear();
        if (!$ta) {
            Log::error('ScheduleController::store - No active academic year found');
            return back()->with('error', 'Tidak ada tahun akademik aktif.');
        }

        try {
            $validated = $request->validate([
                'is_mkdu' => 'required|boolean', // Pastikan ini ada dan boolean
                'course_id' => [
                    Rule::requiredIf($request->input('is_mkdu') == 0), // Wajib jika is_mkdu adalah 0 (Prodi)
                    'nullable', // Bisa null jika tidak diperlukan
                    'exists:courses,id',
                ],
                'mkdu_course_id' => [
                    Rule::requiredIf($request->input('is_mkdu') == 1), // Wajib jika is_mkdu adalah 1 (MKDU)
                    'nullable', // Bisa null jika tidak diperlukan
                    'exists:mkdu_courses,id',
                ],
                'kelas_id' => 'required|exists:kelas,id',
                'room_id' => 'required|exists:rooms,id',
                'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'lecturer1_id' => 'nullable|exists:lecturers,id',
                'lecturer1_start' => 'nullable|integer|min:1|max:16',
                'lecturer1_end' => 'nullable|integer|min:1|max:16|gte:lecturer1_start',
                'lecturer2_id' => 'nullable|exists:lecturers,id',
                'lecturer2_start' => 'nullable|integer|min:1|max:16',
                'lecturer2_end' => 'nullable|integer|min:1|max:16|gte:lecturer2_start',
            ]);
            Log::debug('Validation successful...');

            // Tambahkan validasi kustom untuk dosen kedua agar tidak sama dengan dosen pertama
            if (!empty($validated['lecturer2_id']) && $validated['lecturer2_id'] == $validated['lecturer1_id']) {
                throw ValidationException::withMessages([
                    'lecturer2_id' => 'Dosen kedua tidak boleh sama dengan dosen pertama.'
                ]);
            }

            // Siapkan data jadwal
            $scheduleData = [
                'department_id' => $department->id,
                'academic_year_id' => $ta->id,
                'kelas_id' => $validated['kelas_id'],
                'room_id' => $validated['room_id'],
                'hari' => $validated['hari'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'waktu' => $validated['start_time'], // Consider removing if redundant, as discussed previously
            ];

            // Tentukan schedulable_id dan schedulable_type berdasarkan is_mkdu
            if ($validated['is_mkdu']) {
                $scheduleData['schedulable_id'] = $validated['mkdu_course_id'];
                $scheduleData['schedulable_type'] = MkduCourse::class;
            } else {
                $scheduleData['schedulable_id'] = $validated['course_id'];
                $scheduleData['schedulable_type'] = Course::class;
            }

            // Siapkan data dosen untuk attach/sync
            $lecturersData = [];
            if (!empty($validated['lecturer1_id'])) {
                $lecturersData[$validated['lecturer1_id']] = [
                    'start_pertemuan' => $validated['lecturer1_start'] ?? 1, // Menggunakan nilai default jika null
                    'end_pertemuan' => $validated['lecturer1_end'] ?? 8,   // Menggunakan nilai default jika null
                ];
            }
            if (!empty($validated['lecturer2_id'])) {
                $lecturersData[$validated['lecturer2_id']] = [
                    'start_pertemuan' => $validated['lecturer2_start'] ?? 9,
                    'end_pertemuan' => $validated['lecturer2_end'] ?? 16,
                ];
            }

            // Cek konflik
            $conflictErrors = $this->scheduleService->checkConflict($scheduleData, $lecturersData);

            if (!empty($conflictErrors)) {
                return back()
                    ->withErrors(['conflict' => $conflictErrors]) // Menggunakan key 'conflict' untuk pesan
                    ->withInput();
            }

            // Simpan jadwal dan sinkronkan dosen via service
            $this->scheduleService->createSchedule($scheduleData, $lecturersData);

            return redirect()->route('admin.list-jadwal.show', $department->id)
                ->with('success', 'Jadwal berhasil ditambahkan.');

        } catch (ValidationException $e) {
            Log::error('Validation error during schedule creation: ' . $e->getMessage(), ['errors' => $e->errors()]);
            return back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating schedule: ' . $e->getMessage(), ['data' => $request->all()]);
            return back()
                ->with('error', 'Gagal menambahkan jadwal: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Department $department, Schedule $schedule)
    {
        $schedule->load(['schedulable', 'room', 'kelas', 'lecturers']);
        $this->authorize('update', $schedule);
        $ta = getCurrentAcademicYear();
        if (!$ta) {
            Log::error('ScheduleController::edit - No active academic year found');
            return back()->with('error', 'Tidak ada tahun akademik aktif.');
        }
        $semesterNumbers = $ta->semester === 'Ganjil' ? [1, 3, 5, 7] : [2, 4, 6, 8];
        $courses = Course::where('department_id', $schedule->department_id)
            ->whereIn('semester_number', $semesterNumbers)
            ->orderBy('semester_number')
            ->get();
        $mkduCourses = MkduCourse::whereIn('semester_number', $semesterNumbers)->get();
        $rooms = Room::all();
        $kelas = Kelas::where('department_id', $schedule->department_id)->get();
        // $lecturers = Lecturer::where('department_id', $schedule->department_id)->get();
        $lecturers = Lecturer::orderBy('department_id')->get(); // Mengakomodir dosen luar prodi
        $scheduleLecturers = $schedule->lecturers->mapWithKeys(function ($lecturer, $key) {
            return [
                "lecturer" . ($key + 1) . "_id" => $lecturer->id,
                "lecturer" . ($key + 1) . "_start" => $lecturer->pivot->start_pertemuan,
                "lecturer" . ($key + 1) . "_end" => $lecturer->pivot->end_pertemuan,
            ];
        })->toArray();
        $lecturer1_id = $scheduleLecturers['lecturer1_id'] ?? null;
        $lecturer1_start = $scheduleLecturers['lecturer1_start'] ?? null;
        $lecturer1_end = $scheduleLecturers['lecturer1_end'] ?? null;
        $lecturer2_id = $scheduleLecturers['lecturer2_id'] ?? null;
        $lecturer2_start = $scheduleLecturers['lecturer2_start'] ?? null;
        $lecturer2_end = $scheduleLecturers['lecturer2_end'] ?? null;
        return view('backend.schedule.edit', compact(
            'schedule',
            'courses',
            'mkduCourses',
            'rooms',
            'lecturers',
            'kelas',
            'ta',
            'lecturer1_id',
            'lecturer1_start',
            'lecturer1_end',
            'lecturer2_id',
            'lecturer2_start',
            'lecturer2_end',
            'department'
        ));
    }

    public function update(Request $request, Department $department, Schedule $schedule)
    {
        Log::debug('Entering update() method for schedule: ' . $schedule->id);
        $this->authorize('update', $schedule);
        $ta = getCurrentAcademicYear();
        if (!$ta) {
            Log::error('ScheduleController::update - No active academic year found');
            return back()->with('error', 'Tidak ada tahun akademik aktif.');
        }

        try {
            $validated = $request->validate([
                'is_mkdu' => 'required|boolean',
                'course_id' => [
                    Rule::requiredIf($request->input('is_mkdu') == 0), // Wajib jika is_mkdu == 0
                    'nullable', // PENTING: Bisa null jika is_mkdu == 1
                    'exists:courses,id',
                ],
                'mkdu_course_id' => [
                    Rule::requiredIf($request->input('is_mkdu') == 1), // Wajib jika is_mkdu == 1
                    'nullable', // PENTING: Bisa null jika is_mkdu == 0
                    'exists:mkdu_courses,id',
                ],
                'kelas_id' => 'required|exists:kelas,id',
                'room_id' => 'required|exists:rooms,id',
                'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'lecturer1_id' => 'nullable|exists:lecturers,id',
                'lecturer1_start' => 'nullable|integer|min:1|max:16',
                'lecturer1_end' => 'nullable|integer|min:1|max:16|gte:lecturer1_start',
                'lecturer2_id' => 'nullable|exists:lecturers,id',
                'lecturer2_start' => 'nullable|integer|min:1|max:16',
                'lecturer2_end' => 'nullable|integer|min:1|max:16|gte:lecturer2_start',
            ]);
            Log::debug('Validation successful for update...');

            // Tambahkan validasi kustom untuk dosen kedua agar tidak sama dengan dosen pertama
            if (!empty($validated['lecturer2_id']) && $validated['lecturer2_id'] == $validated['lecturer1_id']) {
                throw ValidationException::withMessages([
                    'lecturer2_id' => 'Dosen kedua tidak boleh sama dengan dosen pertama.'
                ]);
            }

            $scheduleData = [
                'department_id' => $department->id,
                'academic_year_id' => $ta->id,
                'kelas_id' => $validated['kelas_id'],
                'room_id' => $validated['room_id'],
                'hari' => $validated['hari'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'waktu' => $validated['start_time'], // Tetap pertimbangkan untuk menghapus jika redundan
            ];

            if ($validated['is_mkdu']) {
                $scheduleData['schedulable_id'] = $validated['mkdu_course_id'];
                $scheduleData['schedulable_type'] = MkduCourse::class;
            } else {
                $scheduleData['schedulable_id'] = $validated['course_id'];
                $scheduleData['schedulable_type'] = Course::class;
            }

            $lecturersData = [];
            if (!empty($validated['lecturer1_id'])) {
                $lecturersData[$validated['lecturer1_id']] = [
                    'start_pertemuan' => $validated['lecturer1_start'] ?? 1,
                    'end_pertemuan' => $validated['lecturer1_end'] ?? 8, // Di sini Anda mungkin ingin 8, bukan 16
                ];
            }
            if (!empty($validated['lecturer2_id'])) {
                // Pindahkan validasi ini ke atas bersama $request->validate agar terintegrasi lebih baik
                // if ($validated['lecturer2_id'] == $validated['lecturer1_id']) {
                //     throw \Illuminate\Validation\ValidationException::withMessages(['lecturer2_id' => 'Dosen kedua tidak boleh sama dengan dosen pertama.']);
                // }
                $lecturersData[$validated['lecturer2_id']] = [
                    'start_pertemuan' => $validated['lecturer2_start'] ?? 9, // Di sini Anda mungkin ingin 9, bukan 1
                    'end_pertemuan' => $validated['lecturer2_end'] ?? 16,
                ];
            }

            $conflictErrors = $this->scheduleService->checkConflict($scheduleData, $lecturersData, $schedule->id);
            if (!empty($conflictErrors)) {
                return back()
                    ->withErrors(['conflict' => $conflictErrors])
                    ->withInput();
            }

            $this->scheduleService->updateSchedule($schedule, $scheduleData, $lecturersData);
            return redirect()
                ->route('admin.list-jadwal.show', $department->id)
                ->with('success', 'Jadwal berhasil diperbarui.');

        } catch (ValidationException $e) {
            Log::error('Validation error during schedule update: ' . $e->getMessage(), ['errors' => $e->errors()]);
            return back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating schedule: ' . $e->getMessage(), ['schedule_id' => $schedule->id, 'data' => $request->all()]);
            return back()
                ->with('error', 'Gagal memperbarui jadwal: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Department $department, Schedule $schedule)
    {
        $this->authorize('delete', [$schedule, $department]);

        $schedule->delete();

        return redirect()->route('admin.list-jadwal.show', $department->id)
            ->with('success', 'Jadwal berhasil dihapus.');
    }

    protected function user()
    {
        return Auth::user();
    }

    public function import(Request $request, Department $department)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'File import harus diunggah.',
            'file.mimes' => 'Format file tidak valid. Harap gunakan XLSX, XLS, atau CSV.',
            'file.max' => 'Ukuran file terlalu besar (maksimal 2MB).',
        ]);
        $ta = getCurrentAcademicYear();
        if (!$ta) {
            return redirect()->back()->with('error', 'Tidak ada tahun akademik aktif yang ditemukan. Harap atur terlebih dahulu.');
        }
        try {
            $import = new SchedulesImport($department->id, $ta->id);
            Excel::import($import, $request->file('file'));
            if (!empty($import->getErrors())) {
                return redirect()->back()->with('warning', 'Beberapa jadwal berhasil diimport, tetapi ada masalah pada baris tertentu:')->withErrors($import->getErrors());
            }
            return redirect()->back()->with('success', 'Jadwal berhasil diimport!');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Baris " . $failure->row() . ": " . implode(", ", $failure->errors());
            }
            return redirect()->back()->with('error', 'Gagal mengimport jadwal karena data tidak valid.')->withErrors($errors);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimport jadwal: ' . $e->getMessage());
        }
    }

}
