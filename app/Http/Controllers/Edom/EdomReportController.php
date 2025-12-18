<?php

namespace App\Http\Controllers\Edom;

use App\Models\Course;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Response;
use App\Models\Schedule;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\StudyPlan;
use App\Models\Department;
use Illuminate\Support\Str;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Exports\EdomReportExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MkduCourse;


class EdomReportController extends Controller
{
    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderBy('ta', 'desc')->get();
        $departments = Department::all();
        $currentAcademicYear = AcademicYear::where('status', 1)->first();

        $selectedYear = $request->academic_year_id ?? $currentAcademicYear->id;
        $selectedDepartment = $request->department_id;

        $query = Response::with(['schedule.course', 'schedule.lecturersInSchedule'])
            ->where('academic_year_id', $selectedYear);

        if ($selectedDepartment) {
            $query->whereHas('schedule.course', function ($q) use ($selectedDepartment) {
                $q->where('department_id', $selectedDepartment);
            });
        }

        // Hitung rata-rata per kategori
        $categoryAverages = $query->join('questions', 'responses.question_id', '=', 'questions.id')
            ->select(
                'questions.category',
                DB::raw('AVG(responses.rating) as average'),
                DB::raw('COUNT(DISTINCT responses.schedule_id) as course_count'),
                DB::raw('COUNT(DISTINCT responses.student_id) as student_count')
            )
            ->groupBy('questions.category')
            ->get();

        // Hitung rata-rata per dosen
        $lecturerAverages = DB::table('responses')
            ->join('schedules', 'responses.schedule_id', '=', 'schedules.id')
            ->join('lecturer_schedule', 'schedules.id', '=', 'lecturer_schedule.schedule_id')
            ->join('lecturers', 'lecturer_schedule.lecturer_id', '=', 'lecturers.id')
            ->where('responses.academic_year_id', $selectedYear)
            ->when($selectedDepartment, function ($query) use ($selectedDepartment) {
                return $query->join('courses', 'schedules.course_id', '=', 'courses.id')
                    ->where('courses.department_id', $selectedDepartment);
            })
            ->select(
                'lecturers.id as id',
                'lecturers.nama_dosen',
                DB::raw('AVG(responses.rating) as average'),
                DB::raw('COUNT(DISTINCT responses.schedule_id) as course_count'),
                DB::raw('COUNT(DISTINCT responses.student_id) as student_count')
            )
            ->groupBy('lecturers.id', 'lecturers.nama_dosen')
            ->orderBy('average', 'desc')
            ->limit(10)
            ->get();

        return view('admin.edom.reports.index', compact(
            'academicYears',
            'departments',
            'categoryAverages',
            'lecturerAverages',
            'selectedYear',
            'selectedDepartment'
        ));
    }

    public function lecturerDetail($lecturerId)
    {
        $lecturer = Lecturer::with('department')->findOrFail($lecturerId);
        $currentAcademicYear = AcademicYear::where('status', 1)->first();

        if (!$currentAcademicYear) {
            return redirect()->back()->with('error', 'Tidak ada tahun akademik aktif yang ditemukan.');
        }
        $schedules = Schedule::whereHas('lecturersInSchedule', function ($query) use ($lecturerId) {
            $query->where('lecturers.id', $lecturerId);
        })
            ->where('academic_year_id', $currentAcademicYear->id)
            ->where('schedulable_type', Course::class)
            ->with([
                'schedulable',
                'responses.question.categoryName'
            ])
            ->get();

        // Hitung statistik per mata kuliah
        $courseStats = [];
        foreach ($schedules as $schedule) {
            if ($schedule->schedulable instanceof Course) {
                $stats = [
                    'course' => $schedule->schedulable,
                    'respondents' => $schedule->responses->unique('student_id')->count(),
                    'categories' => $schedule->responses
                        ->groupBy('question.categoryName.value')
                        ->map(function ($responses) {
                            return round($responses->avg('rating'), 2);
                        }),
                    'overall' => round($schedule->responses->avg('rating'), 2)
                ];
                $courseStats[] = $stats;
            }
        }

        // Hitung rata-rata keseluruhan dosen ini (bukan 5 teratas)
        $lecturerOverallAverage = DB::table('responses')
            ->join('schedules', 'responses.schedule_id', '=', 'schedules.id')
            ->where('schedules.schedulable_type', Course::class) // Hanya EDOM dari Course
            ->join('lecturer_schedule', 'schedules.id', '=', 'lecturer_schedule.schedule_id')
            ->where('lecturer_schedule.lecturer_id', $lecturerId) // Filter untuk dosen ini
            ->where('responses.academic_year_id', $currentAcademicYear->id)
            ->avg('responses.rating');

        $lecturerOverallAverage = round($lecturerOverallAverage ?? 0, 2);

        // Hitung rata-rata keseluruhan per kategori untuk dosen ini (bukan semua dosen)
        $categoryAverages = Response::whereIn('schedule_id', $schedules->pluck('id'))
            ->join('questions', 'responses.question_id', '=', 'questions.id')
            ->join('edom_categories', 'questions.category', '=', 'edom_categories.id')
            ->select(
                'edom_categories.value as category',
                DB::raw('AVG(responses.rating) as average'),
                DB::raw('COUNT(DISTINCT responses.student_id) as respondent_count')
            )
            ->groupBy('edom_categories.value')
            ->get();

        return view('admin.edom.reports.lecturer_detail', compact(
            'lecturer',
            'currentAcademicYear',
            'courseStats',
            'lecturerOverallAverage',
            'categoryAverages'
        ));
    }

    public function exportExcels(Request $request)
    {
        $academicYear = AcademicYear::findOrFail($request->academic_year_id);
        $lecturerAverages = $this->getLecturerAverages($request);

        return Excel::download(
            new EdomReportExport($lecturerAverages, $academicYear),
            'laporan-edom-' . $academicYear->ta . '-' . $academicYear->semester . '.xlsx'
        );
    }

    public function exportExcel(Request $request)
    {
        $academicYearId = $request->query('academic_year_id');
        $academicYear = $academicYearId
            ? AcademicYear::findOrFail($academicYearId)
            : AcademicYear::where('status', 1)->firstOrFail();

        $lecturerAverages = $this->getLecturerAverages($request);

        return Excel::download(
            new EdomReportExport($lecturerAverages, $academicYear),
            'laporan-edom-' . $academicYear->ta . '-' . $academicYear->semester . '.xlsx'
        );
    }


    public function exportPdf(Request $request)
    {
        // Tambahkan validasi dan default value
        $academicYear = AcademicYear::where('status', 1)->first();
        if ($request->has('academic_year_id')) {
            $academicYear = AcademicYear::findOrFail($request->academic_year_id);
        }

        // $selectedDepartment = $request->department_id;

        try {
            // Ambil data yang diperlukan
            // Perbaikan: Memastikan getCategoryAverages dan getLecturerAverages sudah difilter dengan benar
            $categoryAverages = $this->getCategoryAverages($request);
            $lecturerAverages = $this->getLecturerAverages($request);

            // Generate PDF
            $pdf = PDF::loadView('admin.edom.reports.pdf', compact(
                'academicYear',
                'categoryAverages',
                'lecturerAverages'
            ));

            // Return PDF untuk didownload
            return $pdf->download('laporan-edom-' . $academicYear->ta . '-' . $academicYear->semester . '.pdf');
        } catch (\Exception $e) {
            // Untuk debugging
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    private function calculateScheduleStats($schedule)
    {
        // Perbaikan: Menggunakan categoryName.value dan eager load categoryName
        $responses = $schedule->responses()
            ->with('question.categoryName')
            ->get();

        if ($responses->isEmpty()) {
            return null;
        }

        $categoryAverages = $responses->groupBy('question.categoryName.value') // Group by value
            ->map(function ($items) {
                return round($items->avg('rating'), 2);
            });

        return [
            'schedule' => $schedule,
            'averages' => $categoryAverages,
            'overall_average' => round($responses->avg('rating'), 2),
            'respondent_count' => $responses->unique('student_id')->count()
        ];
    }

    private function getLecturerAverages($request)
    {
        $selectedYear = $request->academic_year_id ?? AcademicYear::where('status', 1)->first()->id;
        $selectedDepartment = $request->department_id;

        // Perbaikan: Menambahkan filter schedulable_type
        return DB::table('responses')
            ->join('schedules', 'responses.schedule_id', '=', 'schedules.id')
            ->where('schedules.schedulable_type', Course::class)
            ->join('lecturer_schedule', 'schedules.id', '=', 'lecturer_schedule.schedule_id')
            ->join('lecturers', 'lecturer_schedule.lecturer_id', '=', 'lecturers.id')
            ->join('departments', 'lecturers.department_id', '=', 'departments.id')
            ->where('responses.academic_year_id', $selectedYear)
            ->when($selectedDepartment, function ($query) use ($selectedDepartment) {
                return $query->where('lecturers.department_id', $selectedDepartment);
            })
            ->select(
                'lecturers.id as id',
                'lecturers.nama_dosen',
                'departments.nama',
                DB::raw('AVG(responses.rating) as average'),
                DB::raw('COUNT(DISTINCT responses.schedule_id) as course_count'),
                DB::raw('COUNT(DISTINCT responses.student_id) as student_count')
            )
            ->groupBy('lecturers.id', 'lecturers.nama_dosen', 'departments.nama')
            ->orderBy('average', 'desc')
            ->get();
    }

    private function getCategoryAverages($request)
    {
        $selectedYear = $request->academic_year_id ?? AcademicYear::where('status', 1)->first()->id;
        $selectedDepartment = $request->department_id;

        $query = Response::where('academic_year_id', $selectedYear);

        // Perbaikan: Menggunakan whereHas dengan nested whereHasMorph
        if ($selectedDepartment) {
            $query->whereHas('schedule', function ($scheduleQuery) use ($selectedDepartment) {
                $scheduleQuery->where('schedulable_type', Course::class)
                    ->whereHasMorph('schedulable', [Course::class], function ($courseQuery) use ($selectedDepartment) {
                        $courseQuery->where('department_id', $selectedDepartment);
                    });
            });
        }

        // Perbaikan: Join ke edom_categories dan select/group by value
        return $query->join('questions', 'responses.question_id', '=', 'questions.id')
            ->join('edom_categories', 'questions.category', '=', 'edom_categories.id')
            ->select(
                'edom_categories.value as category',
                DB::raw('AVG(responses.rating) as average'),
                DB::raw('COUNT(DISTINCT responses.schedule_id) as course_count'),
                DB::raw('COUNT(DISTINCT responses.student_id) as student_count')
            )
            ->groupBy('edom_categories.value')
            ->get();
    }

    public function exportDepartmentPdf(Request $request, $departmentId)
    {
        $department = Department::with('faculty')->findOrFail($departmentId);
        $academicYear = AcademicYear::findOrFail($request->academic_year_id ?? AcademicYear::where('status', 1)->first()->id);

        // Ambil semua data yang diperlukan
        // Perbaikan: Menggunakan schedulable_type dan schedulable_id, join edom_categories
        $categoryAverages = DB::table('responses')
            ->join('questions', 'responses.question_id', '=', 'questions.id')
            ->join('edom_categories', 'questions.category', '=', 'edom_categories.id')
            ->join('schedules', 'responses.schedule_id', '=', 'schedules.id')
            ->where('schedules.schedulable_type', Course::class)
            ->join('courses', function ($join) use ($departmentId) {
                $join->on('schedules.schedulable_id', '=', 'courses.id')
                    ->where('courses.department_id', $departmentId);
            })
            ->where('responses.academic_year_id', $academicYear->id)
            ->select(
                'edom_categories.value as category',
                DB::raw('AVG(responses.rating) as average'),
                DB::raw('COUNT(DISTINCT responses.schedule_id) as course_count'),
                DB::raw('COUNT(DISTINCT responses.student_id) as student_count')
            )
            ->groupBy('edom_categories.value')
            ->get();

        // Perbaikan: Menambahkan filter schedulable_type
        $lecturerAverages = DB::table('responses')
            ->join('schedules', 'responses.schedule_id', '=', 'schedules.id')
            ->where('schedules.schedulable_type', Course::class) // Hanya hitung EDOM dari Course
            ->join('lecturer_schedule', 'schedules.id', '=', 'lecturer_schedule.schedule_id')
            ->join('lecturers', 'lecturer_schedule.lecturer_id', '=', 'lecturers.id')
            ->join('departments', 'lecturers.department_id', '=', 'departments.id')
            ->where('lecturers.department_id', $departmentId)
            ->where('responses.academic_year_id', $academicYear->id)
            ->select(
                'lecturers.id',
                'lecturers.nama_dosen',
                'departments.nama',
                DB::raw('AVG(responses.rating) as average'),
                DB::raw('COUNT(DISTINCT responses.schedule_id) as course_count'),
                DB::raw('COUNT(DISTINCT responses.student_id) as student_count')
            )
            ->groupBy('lecturers.id', 'lecturers.nama_dosen', 'departments.nama')
            ->orderBy('average', 'desc')
            ->get();

        // Perbaikan: Menggunakan whereHasMorph untuk total_courses
        $statistics = [
            'total_courses' => Schedule::where('academic_year_id', $academicYear->id)
                ->where('schedulable_type', Course::class)
                ->whereHasMorph('schedulable', [Course::class], function ($query) use ($departmentId) {
                    $query->where('department_id', $departmentId);
                })
                ->count(),

            'total_students' => Student::where('department_id', $departmentId)->count(),
            'total_lecturers' => Lecturer::where('department_id', $departmentId)->count(),
            'response_rate' => $this->calculateResponseRate($departmentId, $academicYear->id)
        ];

        // Load view PDF
        $pdf = PDF::loadView('admin.edom.reports.department_pdf', compact(
            'department',
            'academicYear',
            'categoryAverages',
            'lecturerAverages',
            'statistics'
        ));

        // Set paper ke landscape untuk data yang lebih lebar
        $pdf->setPaper('a4', 'landscape');

        // Download PDF
        return $pdf->download('laporan-edom-' . Str::slug($department->nama) . '-' .
            $academicYear->ta . '-' . $academicYear->semester . '.pdf');
    }

    public function departmentReport(Request $request, $departmentId)
    {
        $department = Department::findOrFail($departmentId);
        $academicYear = AcademicYear::findOrFail($request->academic_year_id ?? AcademicYear::where('status', 1)->first()->id);

        // Ambil rata-rata per kategori untuk prodi tertentu, menggunakan value dari edom_categories
        $categoryAverages = DB::table('responses')
            ->join('questions', 'responses.question_id', '=', 'questions.id')
            ->join('edom_categories', 'questions.category', '=', 'edom_categories.id')
            ->join('schedules', 'responses.schedule_id', '=', 'schedules.id')
            ->where('schedules.schedulable_type', Course::class) // Filter hanya untuk jadwal mata kuliah prodi
            ->join('courses', function ($join) use ($departmentId) {
                // Join courses menggunakan schedulable_id dan filter department_id
                $join->on('schedules.schedulable_id', '=', 'courses.id')
                    ->where('courses.department_id', $departmentId);
            })
            ->where('responses.academic_year_id', $academicYear->id)
            ->select(
                'edom_categories.value as category',
                DB::raw('AVG(responses.rating) as average'),
                DB::raw('COUNT(DISTINCT responses.schedule_id) as course_count'),
                DB::raw('COUNT(DISTINCT responses.student_id) as student_count')
            )
            ->groupBy('edom_categories.value')
            ->get();

        // Ambil rata-rata per dosen untuk prodi tertentu
        // --- PERBAIKAN: Menambahkan filter schedulable_type untuk konsistensi ---
        $lecturerAverages = DB::table('responses')
            ->join('schedules', 'responses.schedule_id', '=', 'schedules.id')
            ->where('schedules.schedulable_type', Course::class) // Hanya hitung EDOM dari mata kuliah Prodi
            ->join('lecturer_schedule', 'schedules.id', '=', 'lecturer_schedule.schedule_id')
            ->join('lecturers', 'lecturer_schedule.lecturer_id', '=', 'lecturers.id')
            // Join ke tabel courses untuk memfilter dosen yang mengajar di prodi ini
            // melalui mata kuliah prodi (jika diperlukan)
            // Namun, karena dosen memiliki department_id, bisa langsung filter di situ
            // ->join('courses', function($join) {
            //     $join->on('schedules.schedulable_id', '=', 'courses.id');
            // })
            // ->where('courses.department_id', $departmentId) // Ini opsional, tergantung relasi dosen-prodi
            ->join('departments', 'lecturers.department_id', '=', 'departments.id')
            ->where('lecturers.department_id', $departmentId) // Filter dosen berdasarkan departemen
            ->where('responses.academic_year_id', $academicYear->id)
            ->select(
                'lecturers.id',
                'lecturers.nama_dosen',
                'departments.nama',
                DB::raw('AVG(responses.rating) as average'),
                DB::raw('COUNT(DISTINCT responses.schedule_id) as course_count'),
                DB::raw('COUNT(DISTINCT responses.student_id) as student_count')
            )
            ->groupBy('lecturers.id', 'lecturers.nama_dosen', 'departments.nama')
            ->orderBy('average', 'desc')
            ->get();

        // Ambil statistik tambahan
        $statistics = [
            // --- PERBAIKAN: Menggunakan whereHasMorph untuk total_courses ---
            'total_courses' => Schedule::where('academic_year_id', $academicYear->id)
                ->where('schedulable_type', Course::class) // Filter hanya untuk jadwal mata kuliah prodi
                ->whereHasMorph('schedulable', [Course::class], function ($query) use ($departmentId) {
                    $query->where('department_id', $departmentId); // Course harus milik department ini
                })
                ->count(),

            'total_students' => Student::where('department_id', $departmentId)->count(),

            'total_lecturers' => Lecturer::where('department_id', $departmentId)->count(),

            'response_rate' => $this->calculateResponseRate($departmentId, $academicYear->id)
        ];

        return view('admin.edom.reports.department', compact(
            'department',
            'academicYear',
            'categoryAverages',
            'lecturerAverages',
            'statistics'
        ));
    }

    private function calculateResponseRate($departmentId, $academicYearId)
    {
        // Hitung total KRS (StudyPlan)
        // Memastikan hanya StudyPlan yang terkait dengan Course dari department yang dihitung
        $totalEnrollments = StudyPlan::where('academic_year_id', $academicYearId)
            ->whereHas('schedule', function ($scheduleQuery) use ($departmentId) {
                // Di dalam closure scheduleQuery, kita sudah berada di konteks model Schedule
                // Jadi, kita bisa memanggil whereHasMorph langsung pada scheduleQuery
                $scheduleQuery->where('schedulable_type', Course::class)
                    ->whereHasMorph('schedulable', [Course::class], function ($courseQuery) use ($departmentId) {
                    // Di dalam closure courseQuery, kita sudah berada di konteks model Course
                    $courseQuery->where('department_id', $departmentId);
                });
            })
            ->count();

        // Hitung total response (EDOM)
        // Memastikan hanya Response yang terkait dengan Course dari department yang dihitung
        $totalResponses = Response::where('academic_year_id', $academicYearId)
            ->whereHas('schedule', function ($scheduleQuery) use ($departmentId) {
                // Di dalam closure scheduleQuery, kita sudah berada di konteks model Schedule
                $scheduleQuery->where('schedulable_type', Course::class)
                    ->whereHasMorph('schedulable', [Course::class], function ($courseQuery) use ($departmentId) {
                    // Di dalam closure courseQuery, kita sudah berada di konteks model Course
                    $courseQuery->where('department_id', $departmentId);
                });
            })
            ->count();

        // Hitung response rate
        return $totalEnrollments > 0 ? (round(($totalResponses / $totalEnrollments) * 100, 2)) : 0;
    }

    public function departmentsReport(Request $request)
    {
        // Pastikan tahun akademik aktif ditemukan, jika tidak ada, kembalikan error.
        $academicYear = AcademicYear::where('status', 1)->first();
        if (!$academicYear) {
            return redirect()->back()->with('error', 'Tidak ada tahun akademik aktif yang ditemukan. Harap atur terlebih dahulu.');
        }
        $academicYearId = $request->academic_year_id ?? $academicYear->id;
        $currentAcademicYear = AcademicYear::findOrFail($academicYearId);
        // Ambil semua department dengan statistik
        $departments = Department::withCount(['students', 'lecturers'])
            ->get()
            ->map(function ($department) use ($currentAcademicYear) {
                // Hitung total mata kuliah (hanya Course yang terkait dengan Department)
                // Menggunakan whereHasMorph untuk schedulable_type Course
                $department->course_count = Schedule::where('academic_year_id', $currentAcademicYear->id)
                    ->where('schedulable_type', Course::class) // Hanya jadwal yang bertipe Course
                    ->whereHasMorph('schedulable', [Course::class], function ($query) use ($department) {
                    $query->where('department_id', $department->id); // Course harus milik department ini
                })
                    ->count();

                // Hitung rata-rata EDOM (hanya untuk Course yang terkait dengan Department)
                $averageRating = DB::table('responses')
                    ->join('schedules', 'responses.schedule_id', '=', 'schedules.id')
                    ->where('schedules.schedulable_type', Course::class) // Filter hanya untuk Course
                    ->join('courses', function ($join) use ($department) {
                    // Join courses dengan schedulable_id dan filter department_id
                    $join->on('schedules.schedulable_id', '=', 'courses.id')
                        ->where('courses.department_id', $department->id);
                })
                    ->where('responses.academic_year_id', $currentAcademicYear->id)
                    ->avg('responses.rating');

                $department->average_rating = $averageRating ?? 0;

                // Hitung response rate
                $department->response_rate = $this->calculateResponseRate($department->id, $currentAcademicYear->id);

                return $department;
            });

        return view('admin.edom.reports.departments', compact('departments', 'currentAcademicYear'));
    }
}
