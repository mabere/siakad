<?php

namespace App\Http\Controllers\Backend;

use App\Models\Grade;
use App\Models\Kelas;
use App\Models\Alumni;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Student;
use App\Models\Addition;
use App\Models\Kegiatan;
use App\Models\Lecturer;
use App\Models\Response;
use App\Models\Schedule;
use App\Models\StudyPlan;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\MkduCourse;
use App\Models\ThesisExam;
use App\Models\AcademicYear;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Models\LetterRequest;
use App\Models\Questionnaire;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentSemesterStatus;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $role = Auth::user()->activeRole();

        return match ($role) {
            'admin' => $this->adminDashboard(),
            'dekan' => $this->dekanDashboard(),
            'kaprodi' => $this->kaprodiDashboard(),
            'dosen' => $this->dosenDashboard($request),
            'mahasiswa' => $this->mahasiswaDashboard($request),
            'staff' => $this->staffDashboard(),
            'ktu' => $this->ktuDashboard(),
            'ujm' => $this->ujmDashboard(),
            default => abort(403, 'Dashboard untuk peran ini belum tersedia.'),
        };
    }

    private function adminDashboard()
    {
        $ta = AcademicYear::active();

        // Statistik dasar
        $totalStudents = Student::count();
        $totalLecturers = Lecturer::count();
        $totalFaculties = Faculty::count();
        $totalDepartments = Department::count();

        // Distribusi Gender
        $genderDistribution = Student::selectRaw('gender, COUNT(*) as total')
            ->groupBy('gender')->pluck('total', 'gender');

        // Mahasiswa per Fakultas
        $studentsPerFaculty = Faculty::withCount(['students'])->get();

        // Mahasiswa per Tahun Angkatan (misal: tahun masuk)
        $studentsPerYear = Student::selectRaw('entry_year as year, COUNT(*) as total')
            ->groupBy('year')->orderBy('year')->pluck('total', 'year');

        $data = [
            'facultyCount' => Faculty::count(),
            'departmentCount' => Department::count(),
            'studentCount' => Student::count(), // Total mahasiswa keseluruhan
            'lecturerCount' => Lecturer::count(),
            'courseCount' => Course::count(),
            'alumniCount' => Alumni::count(), // Total alumni
            'activeStudentCount' => Student::whereHas('studentSemesterStatus', function ($query) {
                $query->where('status', 'aktif');
            })->count(),
        ];

        // Ambil tahun akademik aktif (asumsi ada kolom is_active di academic_years)
        $activeAcademicYear = AcademicYear::where('status', 1)->first();
        $activeYearId = $activeAcademicYear ? $activeAcademicYear->id : AcademicYear::latest('id')->first()->id;

        // Statistik alumni untuk dashboard
        $totalAlumni = Alumni::count();
        $employed = Alumni::whereNotNull('job_title')->count();
        $furtherEducation = Alumni::whereNotNull('further_education')->count();
        $contributing = Alumni::whereNotNull('contribution')->count();
        $alumniChartData = [
            'labels' => ['Bekerja', 'Menganggur', 'Pendidikan Lanjutan'],
            'datasets' => [
                [
                    'data' => [
                        $employed,
                        $totalAlumni - $employed - $furtherEducation,
                        $furtherEducation
                    ],
                    'backgroundColor' => ['#36A2EB', '#FF6384', '#FFCE56']
                ]
            ]
        ];

        // Data status mahasiswa dari student_semester_status untuk tahun akademik aktif
        $statusData = StudentSemesterStatus::where('academic_year_id', $activeYearId)
            ->select('status', DB::raw('COUNT(DISTINCT student_id) as total'))
            ->groupBy('status')
            ->get();
        $statusChartData = [
            'labels' => ['Aktif', 'Non-Aktif', 'Cuti', 'DO', 'Lulus'],
            'datasets' => [
                [
                    'data' => [
                        $statusData->where('status', 'aktif')->first()->total ?? 0,
                        $statusData->where('status', 'non-aktif')->first()->total ?? 0,
                        $statusData->where('status', 'cuti')->first()->total ?? 0,
                        $statusData->where('status', 'do')->first()->total ?? 0,
                        $statusData->where('status', 'lulus')->first()->total ?? 0,
                    ],
                    'backgroundColor' => ['#36A2EB', '#FF6384', '#FFCE56', '#FF5733', '#4BC0C0']
                ]
            ]
        ];

        // Update activeStudentCount berdasarkan status 'aktif' di tahun akademik aktif
        $data['activeStudentCount'] = $statusData->where('status', 'aktif')->first()->total ?? 0;

        // Pie Chart: Distribusi Gender Mahasiswa
        $genderData = Student::select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')
            ->get();
        $genderChartData = [
            'labels' => ['Laki-Laki', 'Perempuan'],
            'datasets' => [
                [
                    'data' => [
                        $genderData->where('gender', 'Laki-Laki')->first()->total ?? 0,
                        $genderData->where('gender', 'Perempuan')->first()->total ?? 0
                    ],
                    'backgroundColor' => ['#36A2EB', '#FF6384']
                ]
            ]
        ];

        // Bar Chart: Mahasiswa per Fakultas (dengan singkatan)
        $facultiesWithStudents = Faculty::select('faculties.nama')
            ->leftJoin('departments', 'faculties.id', '=', 'departments.faculty_id')
            ->leftJoin('students', 'departments.id', '=', 'students.department_id')
            ->groupBy('faculties.id', 'faculties.nama')
            ->select(
                DB::raw("CASE
                    WHEN faculties.nama = 'Fakultas Keguruan dan Ilmu Pendidikan' THEN 'FKIP'
                    WHEN faculties.nama = 'Fakultas Teknik' THEN 'TEKNIK'
                    WHEN faculties.nama = 'Fakultas Ekonomi dan Bisnis' THEN 'EKBIS'
                    WHEN faculties.nama = 'Fakultas Hukum' THEN 'HUKUM'
                    WHEN faculties.nama = 'Fakultas Administrasi Publik' THEN 'ADMP'
                    WHEN faculties.nama = 'Fakultas Pertanian' THEN 'PERTANIAN'
                    ELSE faculties.nama END as nama"),
                DB::raw('COUNT(students.id) as students_count')
            )->get();

        // Bar Chart: Mahasiswa per Department (Program Studi) dengan singkatan
        $departmentsWithStudents = Department::select('departments.nama')
            ->leftJoin('students', 'departments.id', '=', 'students.department_id')
            ->groupBy('departments.id', 'departments.nama')
            ->select(
                DB::raw("CASE
                    WHEN departments.nama = 'Program Studi Pendidikan Matematika' THEN 'MTK'
                    WHEN departments.nama = 'Program Studi Pendidikan Bahasa Inggris' THEN 'PBI'
                    WHEN departments.nama = 'Program Studi Pendidikan Bahasa dan Sastra Indonesia' THEN 'PBSI'
                    WHEN departments.nama = 'Program Studi Ilmu Hukum' THEN 'HKM'
                    ELSE LEFT(departments.nama, 5) END as nama"),
                DB::raw('COUNT(students.id) as students_count')
            )->get();

        // Daftar warna untuk setiap bar
        $colors = [
            '#FF6384',
            '#36A2EB',
            '#FFCE56',
            '#4BC0C0',
            '#9966FF',
            '#FF9F40',
            '#E7E9ED',
            '#C9CB3F',
            '#FF5733',
            '#6D8299',
        ];

        $departmentColors = [];
        foreach ($departmentsWithStudents as $index => $department) {
            $departmentColors[] = $colors[$index % count($colors)];
        }

        $departmentChartData = [
            'labels' => $departmentsWithStudents->pluck('nama')->toArray(),
            'datasets' => [
                [
                    'label' => 'Jumlah Mahasiswa',
                    'data' => $departmentsWithStudents->pluck('students_count')->toArray(),
                    'backgroundColor' => $departmentColors,
                ]
            ]
        ];

        $barChartData = [
            'labels' => $facultiesWithStudents->pluck('nama')->toArray(),
            'datasets' => [
                [
                    'label' => 'Jumlah Mahasiswa',
                    'data' => $facultiesWithStudents->pluck('students_count')->toArray(),
                    'backgroundColor' => '#36A2EB'
                ]
            ]
        ];

        // Bar Chart: Mata Kuliah per Fakultas (dengan singkatan)
        $facultiesWithCourses = Faculty::select('faculties.nama')
            ->leftJoin('departments', 'faculties.id', '=', 'departments.faculty_id')
            ->leftJoin('courses', 'departments.id', '=', 'courses.department_id')
            ->groupBy('faculties.id', 'faculties.nama')
            ->select(
                DB::raw("CASE
                    WHEN faculties.nama = 'Keguruan dan Ilmu Pendidikan' THEN 'FKIP'
                    WHEN faculties.nama = 'Teknik' THEN 'TEKNIK'
                    WHEN faculties.nama = 'Ekonomi dan Bisnis' THEN 'EKBIS'
                    WHEN faculties.nama = 'Hukum' THEN 'HUKUM'
                    WHEN faculties.nama = 'Administrasi Publik' THEN 'ADMP'
                    WHEN faculties.nama = 'Pertanian' THEN 'PERTANIAN'
                    ELSE faculties.nama END as nama"),
                DB::raw('COUNT(courses.id) as courses_count')
            )->get();

        $courseChartData = [
            'labels' => $facultiesWithCourses->pluck('nama')->toArray(),
            'datasets' => [
                [
                    'label' => 'Jumlah Mata Kuliah',
                    'data' => $facultiesWithCourses->pluck('courses_count')->toArray(),
                    'backgroundColor' => '#4BC0C0'
                ]
            ]
        ];

        return view('backend.dashboard.index', compact(
            'data',
            'genderChartData',
            'barChartData',
            'courseChartData',
            'statusChartData',
            'alumniChartData',
            'departmentChartData',
            'ta',
            'totalStudents',
            'totalLecturers',
            'totalFaculties',
            'totalDepartments',
            'genderDistribution',
            'studentsPerFaculty',
            'studentsPerYear'
        ))
            ->with('success', 'Selamat datang. Anda berhasil login.');
    }

    public function kaprodiDashboard()
    {
        $user = Auth::user()->load('lecturer.department');
        // Validate user permissions
        if (!$user->hasRole('kaprodi') || !$user->lecturer || !$user->lecturer->department || !$user->lecturer->department->faculty_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses dashboard.');
        }

        $departmentId = $user->lecturer->department->id;
        $facultyId = $user->lecturer->department->faculty_id;
        $department = $user->lecturer->department->nama;
        $ta = getCurrentAcademicYear();

        $stats = Cache::remember("kaprodi_stats_{$departmentId}", 3600, function () use ($departmentId) {
            return [
                'totalMahasiswa' => Student::where('department_id', $departmentId)->count(),
                'totalKelas' => Kelas::where('department_id', $departmentId)->count(),
                'totalDosen' => Lecturer::where('department_id', $departmentId)->count(),
                'mahasiswaTanpaKelas' => Student::where('department_id', $departmentId)
                    ->whereNull('kelas_id')
                    ->count(),

                'totalSuratMasuk' => LetterRequest::whereHas('letterType', function ($q) use ($departmentId) {
                    $q->where('level', 'department')
                        ->whereExists(function ($subQuery) use ($departmentId) {
                            $subQuery->select(DB::raw(1))
                                ->from('letter_type_assignments')
                                ->whereColumn('letter_type_assignments.letter_type_id', 'letter_types.id')
                                ->where('letter_type_assignments.department_id', $departmentId);
                        });
                })->whereHas('user.student', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })->count(),
            ];
        });

        $topIpkStudents = $this->getTopIpkStudentsByDepartment($departmentId, $ta->id);

        $ipkDistribution = $this->getIpkDistribution($departmentId, $ta->id);


        $suratTerbaru = LetterRequest::whereHas('letterType', function ($query) use ($departmentId, $facultyId) {
            $query->whereExists(function ($subQuery) use ($departmentId, $facultyId) {
                $subQuery->select(DB::raw(1))
                    ->from('letter_type_assignments')
                    ->whereColumn('letter_type_assignments.letter_type_id', 'letter_types.id')
                    ->where(function ($q) use ($departmentId, $facultyId) {
                        $q->where('letter_type_assignments.department_id', $departmentId)
                            ->orWhere('letter_type_assignments.faculty_id', $facultyId);
                    });
            });
        })->where(function ($q) use ($departmentId) {
            $q->whereHas('user.student', fn($q) => $q->where('department_id', $departmentId))
                ->orWhereHas('user.lecturer', fn($q) => $q->where('department_id', $departmentId));
        })->with(['user', 'letterType'])
            ->latest()
            ->take(3)
            ->get();

        $kelas = Kelas::where('department_id', $departmentId)
            ->with(['lecturer'])
            ->latest()
            ->take(5)
            ->get();

        $notifikasi = [];
        if ($stats['mahasiswaTanpaKelas'] > 0) {
            $notifikasi[] = "Ada {$stats['mahasiswaTanpaKelas']} mahasiswa belum dialokasikan ke kelas.";
        }

        return view('backend.dashboard.index', array_merge($stats, compact(
            'topIpkStudents',
            'ipkDistribution',
            'suratTerbaru',
            'kelas',
            'notifikasi',
            'department',
            'ta'
        )));

    }

    public function dekanDashboard()
    {
        $user = Auth::user()->load('lecturer.department');
        $userRole = $user->activeRoles;
        // Validate user permissions
        if (!$user->hasRole('dekan') || !$user->lecturer || !$user->lecturer->department || !$user->lecturer->department->faculty_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses dashboard.');
        }

        $fakultasId = $user->lecturer->department->faculty_id;
        $ta = getCurrentAcademicYear();
        $departmentId = $user->lecturer->department_id;
        $stats = Cache::remember("dekan_stats_{$fakultasId}", 3600, function () use ($fakultasId) {
            return [
                'totalMahasiswa' => Student::whereHas('department', fn($q) => $q->where('faculty_id', $fakultasId))->count(),
                'totalDosen' => Lecturer::whereHas('department', fn($q) => $q->where('faculty_id', $fakultasId))->count(),
                'totalProdi' => Department::where('faculty_id', $fakultasId)->count(),
                'totalSuratMasuk' => LetterRequest::whereHas('letterType', function ($q) use ($fakultasId) {
                    $q->whereExists(function ($subQuery) use ($fakultasId) {
                        $subQuery->select(DB::raw(1))
                            ->from('letter_type_assignments')
                            ->whereColumn('letter_type_assignments.letter_type_id', 'letter_types.id')
                            ->where('letter_type_assignments.faculty_id', $fakultasId);
                    });
                })->where(function ($q) use ($fakultasId) {
                    $q->whereHas('user.student.department', fn($q) => $q->where('faculty_id', $fakultasId))
                        ->orWhereHas('user.lecturer.department', fn($q) => $q->where('faculty_id', $fakultasId));
                })->count(),
            ];
        });

        $topIpkStudents = Grade::getTopIpkStudents($fakultasId, $ta->id);
        $suratTerbaru = LetterRequest::whereHas('letterType', function ($query) use ($fakultasId) {
            $query->whereExists(function ($subQuery) use ($fakultasId) {
                $subQuery->select(DB::raw(1))
                    ->from('letter_type_assignments')
                    ->whereColumn('letter_type_assignments.letter_type_id', 'letter_types.id')
                    ->where('letter_type_assignments.faculty_id', $fakultasId);
            });
        })->where(function ($q) use ($fakultasId) {
            $q->whereHas('user.student.department', fn($q) => $q->where('faculty_id', $fakultasId))
                ->orWhereHas('user.lecturer.department', fn($q) => $q->where('faculty_id', $fakultasId));
        })->with(['user', 'letterType'])
            ->latest()
            ->take(3)
            ->get();
        $pengumuman = Announcement::where('faculty_id', $fakultasId)
            ->latest()
            ->take(3)
            ->get();

        // $kegiatans = Kegiatan::where('faculty_id', $fakultasId)
        //     ->get()
        //     ->map(fn($event) => [
        //         'title' => $event->name,
        //         'start_date' => $event->start_date,
        //         'end_date' => $event->end_date ?? $event->start_date,
        //         'url' => $event->url ?? route('dekan.kegiatan.show', $event->id),
        //     ]);

        $events = Kegiatan::forUser($user)
            ->with(['faculty', 'department', 'academicYear'])
            ->get();
        $kegiatans = $events->map(function ($event) {
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


        return view('backend.dashboard.index', array_merge($stats, compact(
            'suratTerbaru',
            'pengumuman',
            'kegiatans',
            'topIpkStudents',
            'ta'
        )));
    }

    public function dosenDashboard(Request $request)
    {
        $ta = getCurrentAcademicYear();
        $dosen = auth()->user()->lecturer;
        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan');
        }

        $jadwal = $this->getCurrentSchedulesCount($dosen, $ta);
        $academicYears = AcademicYear::orderBy('ta', 'desc')
            ->orderBy('semester', 'desc')
            ->get();
        $selectedTa = $request->get('academic_year_id');
        $teachingHistory = $this->getTeachingHistory($dosen, $selectedTa);
        $statistics = $this->getLecturerStatistics($dosen);
        $roles = $this->getLecturerRoles($dosen);

        session()->pull('success', 'Selamat datang di Sistem Informasi Akademik Fakultas.');

        return view('backend.dashboard.index', array_merge([
            'dosen' => $dosen,
            'jadwal' => $jadwal,
            'ta' => $ta,
            'teachingHistory' => $teachingHistory,
            'academicYears' => $academicYears,
            'selectedTa' => $selectedTa,
            'roles' => $roles,
        ], $statistics));
    }

    public function mahasiswaDashboard(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('mahasiswa')) {
            return redirect()->route('welcome')->with('error', 'Silakan login sebagai mahasiswa');
        }
        $user->loadMissing('student');
        $student = $user->student;
        if (!$student) {
            return redirect()->route('welcome')->with('error', 'Data mahasiswa tidak ditemukan');
        }
        $ta = getCurrentAcademicYear();
        // Hitung IPK
        $ipk = Grade::calculateIpk($student->id, $ta->id);
        // Tentukan semester aktif
        $currentSemester = $student->getCurrentSemester($ta->ta, $ta->semester);
        // Ambil atau buat status semester
        $status = $student->statusSemesters()
            ->where('academic_year_id', $ta->id)
            ->where('semester', $currentSemester)
            ->latest()
            ->first();
        if (!$status) {
            $status = $student->statusSemesters()->create([
                'academic_year_id' => $ta->id,
                'semester' => $currentSemester,
                'status' => 'aktif',
                'effective_date' => now(),
            ]);
        }
        $statusText = $status->status;
        // Ambil study plan dan muat relasi dengan schedule dan schedulable-nya
        $studyPlans = StudyPlan::where('student_id', $student->id)
            ->where('academic_year_id', $ta->id)
            ->with([
                'schedule' => function ($query) {
                    $query->with([
                        'schedulable' => function ($morphTo) {
                            $morphTo->morphWith([
                                Course::class => [],
                                MkduCourse::class => [],
                            ]);
                        },
                    ]);
                },
            ])
            ->get();
        foreach ($studyPlans as $studyPlan) {
            $baseData = [
                'student_id' => $student->id,
                'academic_year_id' => $studyPlan->academic_year_id,
                'study_plan_id' => $studyPlan->id,
                'schedule_id' => $studyPlan->schedule_id,
            ];
            // Buat atau temukan Grade
            Grade::firstOrCreate(
                [
                    'study_plan_id' => $studyPlan->id,
                    'schedule_id' => $studyPlan->schedule_id,
                ],
                $baseData
            );
            // Buat atau temukan Attendance
            Attendance::firstOrCreate(
                [
                    'study_plan_id' => $studyPlan->id,
                    'schedule_id' => $studyPlan->schedule_id,
                ],
                $baseData
            );
        }

        $questionnaires = Questionnaire::with('questions')->where('is_active', true)->get();
        // Ambil riwayat IPK
        $ipkHistory = Grade::getIpkHistory($student->id);
        $labels = $ipkHistory->pluck('year')->toArray();
        $data = $ipkHistory->pluck('ipk')->toArray();

        // Validasi EDOM
        $grades = Grade::where('student_id', $student->id)
            ->where('academic_year_id', $ta->id)
            ->where('validation_status', 'locked')
            ->with(['schedule.schedulable'])
            ->get();
        $incompleteEvaluations = [];
        foreach ($grades as $grade) {
            // Pastikan schedule ada dan schedulable juga ada sebelum mengaksesnya
            if ($grade->schedule && $grade->schedule->schedulable) {
                $response = Response::where('student_id', $student->id)
                    ->where('schedule_id', $grade->schedule_id)
                    ->whereHas('questionnaire', fn($q) => $q->active())
                    ->exists();

                if (!$response) {
                    $incompleteEvaluations[] = [
                        'schedule_id' => $grade->schedule_id,
                        'course_name' => $grade->schedule->schedulable->name ?? 'N/A',
                    ];
                }
            }
        }
        return view('backend.dashboard.index', [
            'mahasiswa' => $user,
            'student' => $student,
            'questionnaires' => $questionnaires,
            'studyPlans' => $studyPlans,
            'ipk' => $ipk,
            'currentSemester' => $currentSemester,
            'status' => $statusText,
            'ipkLabels' => $labels,
            'ipkData' => $data,
            'hasIncompleteEdom' => count($incompleteEvaluations) > 0,
            'incompleteEdomCount' => count($incompleteEvaluations),
            'incompleteEvaluations' => $incompleteEvaluations,
        ]);
    }

    public function staffDashboard()
    {
        $staff = Auth::user()->employee;

        if (!$staff || !$staff->department) {
            return redirect()->route('dashboard')->with('error', 'Data departemen tidak ditemukan.');
        }

        $ta = getCurrentAcademicYear();
        $items = Schedule::where('academic_year_id', $ta->id)->where('department_id', $staff->department_id)->with(['course', 'room', 'lecturersInSchedule'])->get();

        $latestJadwal = $items->sortByDesc('created_at')->take(4);

        $jadwalKuliah = $items->count();

        $dosenCount = Lecturer::where('department_id', $staff->department_id)->count();
        $mahasiswaCount = Student::where('department_id', $staff->department_id)->count();
        $courseCount = Course::where('department_id', $staff->department_id)->count();
        $recentValidationChanges = Schedule::with(['course', 'mkduCourse', 'kelas', 'grades.student'])
            ->whereHas('grades', function ($query) {
                $query->whereIn('validation_status', ['pending', 'dosen_validated', 'kaprodi_approved', 'locked']);
            })
            ->where('department_id', $staff->department_id) // Filter berdasarkan department staff
            ->orderBy('updated_at', 'desc') // Urutkan berdasarkan perubahan terbaru
            ->take(5)
            ->get();
        return view('backend.dashboard.index', compact('recentValidationChanges', 'dosenCount', 'mahasiswaCount', 'courseCount', 'jadwalKuliah', 'latestJadwal'));

    }

    public function ktuDashboard()
    {
        $user = Auth::user();
        $ta = getCurrentAcademicYear();
        // Pastikan user memiliki data employee, level 'faculty', dan faculty_id
        if (!$user->employee || $user->employee->level !== 'faculty' || !$user->employee->faculty_id) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Data profil KTU tidak valid atau tidak lengkap.');
        }

        $ktuFacultyId = $user->employee->faculty_id;

        // ===============================================
        // A. PENGAMBILAN DATA UNTUK STATISTIK (KPIs)
        // ===============================================

        // 1. Total Dosen dan Mahasiswa di Fakultas
        $dosenCount = Lecturer::where('faculty_id', $ktuFacultyId)->count();
        $mahasiswaCount = Student::where('faculty_id', $ktuFacultyId)->count();
        $departmentsCount = Department::where('faculty_id', $ktuFacultyId)->count();

        // 2. Distribusi Mahasiswa per Departemen (untuk Bar Chart)
        $studentsPerDepartment = Student::select('department_id', DB::raw('count(*) as total'))
            ->where('faculty_id', $ktuFacultyId)
            ->groupBy('department_id')
            ->with('department') // Asumsikan ada relasi ke tabel departments
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->department->nama ?? 'Tidak Diketahui',
                    'total' => $item->total,
                ];
            });

        // 3. Distribusi Status Mahasiswa (untuk Pie Chart)
        $studentStatusDistribution = StudentSemesterStatus::select('status', DB::raw('count(*) as total'))
            ->where('academic_year_id', $ta->id) // Filter berdasarkan tahun akademik aktif
            ->whereHas('student', function ($query) use ($ktuFacultyId) {
                $query->where('faculty_id', $ktuFacultyId); // Filter berdasarkan fakultas
            })
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => ucfirst($item->status),
                    'total' => $item->total,
                ];
            });

        // 4. Distribusi Status Permohonan Ujian (untuk Gauge/Progress Chart)
        $examStatusDistribution = ThesisExam::select('status', DB::raw('count(*) as total'))
            ->whereHas('thesis.student.department', function ($query) use ($ktuFacultyId) {
                $query->where('faculty_id', $ktuFacultyId);
            })
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => ucfirst(str_replace('_', ' ', $item->status)),
                    'total' => $item->total,
                ];
            });


        // ===============================================
        // B. PENGAMBILAN DATA UNTUK INFORMASI RINCI
        // ===============================================

        // Ambil daftar departemen secara rinci
        $departments = Department::where('faculty_id', $ktuFacultyId)->get();

        // Ambil permohonan ujian yang masih menunggu (untuk Quick Links)
        $pendingExams = ThesisExam::whereIn('status', ['diajukan', 'sedang_dinilai'])
            ->whereHas('thesis.student.department', function ($query) use ($ktuFacultyId) {
                $query->where('faculty_id', $ktuFacultyId);
            })
            ->with('thesis.student.department')
            ->latest()
            ->get();

        // ===============================================
        // C. MENGIRIM DATA KE VIEW
        // ===============================================

        return view('backend.dashboard.index', compact(
            'dosenCount',
            'mahasiswaCount',
            'departmentsCount',
            'studentsPerDepartment',
            'studentStatusDistribution',
            'examStatusDistribution',
            'departments',
            'pendingExams'
        ));
    }

    // Di dalam controller, tambahkan fungsi helper untuk mengubah nama jadi singkatan
    public function getFacultyAbbreviation($nama)
    {
        $abbreviations = [
            'Keguruan dan Ilmu Pendidikan' => 'FKIP',
            'Teknik' => 'Teknik',
            'Ekonomi dan Bisnis' => 'EKBIS',
            'Hukum' => 'HUKUM',
            'Administrasi Publik' => 'ADMP',
            'Pertanian' => 'PERTANIAN'
        ];
        return $abbreviations[$nama] ?? $nama;
    }

    private function getTopIpkStudentsByDepartment($departmentId, $academicYearId)
    {
        $academicYear = AcademicYear::find($academicYearId) ?? AcademicYear::where('status', 1)->first();
        if (!$academicYear) {
            return collect([]);
        }

        // untuk semua semester
        $grades = Grade::where('academic_year_id', 1)
            ->whereNotNull('nhuruf')
            ->whereHas('student', fn($q) => $q->where('department_id', $departmentId))
            ->with(['student', 'schedule.course'])
            ->get();

        $studentIpk = $grades->groupBy('student_id')->map(function ($grades) {
            $totalSks = $totalNilai = 0;
            foreach ($grades as $grade) {
                $sks = $grade->schedule->course->sks ?? 0;
                $nilai = Grade::convertNhurufToAngka($grade->nhuruf);
                if ($sks && $nilai !== null) {
                    $totalSks += $sks;
                    $totalNilai += $sks * $nilai;
                }
            }
            $ipk = $totalSks ? $totalNilai / $totalSks : 0;
            $student = $grades->first()->student;

            return [
                'name' => optional($student)->nama_mhs ?? 'Unknown',
                'ipk' => number_format($ipk, 2),
                'nim' => optional($student)->nim ?? 'Unknown',
                'department' => optional($student->department)->nama ?? 'Unknown'
            ];
        })->sortByDesc('ipk')->take(3)->values();

        return $studentIpk;
    }

    private function getIpkDistribution($departmentId, $academicYearId)
    {
        $academicYear = AcademicYear::find($academicYearId) ?? AcademicYear::where('status', 1)->first();
        if (!$academicYear) {
            return ['1.0-2.0' => 0, '2.0-3.0' => 0, '3.0-4.0' => 0];
        }

        $grades = Grade::where('academic_year_id', $academicYear->id)
            ->whereNotNull('nhuruf')
            ->whereHas('student', fn($q) => $q->where('department_id', $departmentId))
            ->with(['student', 'schedule.course'])
            ->get();

        $studentIpk = $grades->groupBy('student_id')->map(function ($grades) {
            $totalSks = $totalNilai = 0;
            foreach ($grades as $grade) {
                $sks = $grade->schedule->course->sks ?? 0;
                $nilai = Grade::convertNhurufToAngka($grade->nhuruf);
                if ($sks && $nilai !== null) {
                    $totalSks += $sks;
                    $totalNilai += $sks * $nilai;
                }
            }
            return $totalSks ? $totalNilai / $totalSks : 0;
        });

        // Kelompokkan IPK
        $distribution = [
            '1.0-2.0' => 0,
            '2.0-3.0' => 0,
            '3.0-4.0' => 0,
        ];

        foreach ($studentIpk as $ipk) {
            if ($ipk >= 1.0 && $ipk < 2.0) {
                $distribution['1.0-2.0']++;
            } elseif ($ipk >= 2.0 && $ipk < 3.0) {
                $distribution['2.0-3.0']++;
            } elseif ($ipk >= 3.0 && $ipk <= 4.0) {
                $distribution['3.0-4.0']++;
            }
        }

        return $distribution;
    }

    private function getCurrentSchedulesCount($dosen, $ta)
    {
        return Schedule::where('academic_year_id', $ta->id)
            ->whereHas('lecturersInSchedule', function ($query) use ($dosen) {
                $query->where('lecturer_id', $dosen->id);
            })
            ->with('course')
            ->count();
    }

    private function getTeachingHistory($dosen, $selectedTa)
    {
        $query = Schedule::query()
            ->distinct()
            ->select('schedules.*')
            ->join('lecturer_schedule', 'schedules.id', '=', 'lecturer_schedule.schedule_id')
            ->where('lecturer_schedule.lecturer_id', $dosen->id)
            ->with(['course', 'academicYear', 'kelas']);

        if ($selectedTa) {
            $query->where('schedules.academic_year_id', $selectedTa);
        }

        return $query->orderBy('schedules.academic_year_id', 'desc')
            ->limit(3)
            ->get()
            ->groupBy('academic_year_id');
    }


    private function getLecturerStatistics($dosen)
    {
        $ta = getCurrentAcademicYear();
        $schedules = Schedule::where('academic_year_id', $ta->id)
            ->whereHas('lecturersInSchedule', function ($query) use ($dosen) {
                $query->where('lecturer_id', $dosen->id);
            })
            ->with(['responses', 'attendances.attendanceDetails'])
            ->get();

        $totalRatings = 0;
        $totalResponses = 0;
        $totalPresent = 0;
        $totalMeetings = 0;
        $totalAttendances = 0;

        foreach ($schedules as $schedule) {
            foreach ($schedule->responses as $response) {
                $totalRatings += $response->rating;
                $totalResponses++;
            }
            $attendances = $schedule->attendances;
            $totalAttendances += $attendances->count();
            $presentCount = $attendances->flatMap->attendanceDetails->where('status', 'Hadir')->count();
            $totalMeetings += 16 * $attendances->count(); // Asumsi 16 pertemuan per jadwal
            $totalPresent += $presentCount;
        }

        $averageRating = $totalResponses > 0 ? number_format($totalRatings / $totalResponses, 2) : 0;
        $attendancePercentage = $totalMeetings > 0 ? number_format(($totalPresent / $totalMeetings) * 100, 2) : 0;

        // Hitung jumlah unsur penunjang yang divalidasi
        $validatedAdditions = Addition::where('lecturer_id', $dosen->id)
            ->where('status', 'approved') // Asumsi status 'validated' untuk yang disetujui
            ->count();

        return [
            'publikasi' => $dosen->publications->count(),
            'pkm' => $dosen->services->count(),
            'averageRating' => $averageRating,
            'attendancePercentage' => $attendancePercentage,
            'validatedAdditions' => $validatedAdditions,
        ];
    }

    private function getLecturerRoles($dosen)
    {
        // Assuming there's a roles relationship in your User model
        $userRoles = auth()->user()->roles;

        // Map roles to a more readable format
        return $userRoles->map(function ($role) {
            return [
                'name' => $role->name,
                'display_name' => $role->display_name ?? $this->formatRoleName($role->name),
                'description' => $role->description
            ];
        });
    }

    private function formatRoleName($name)
    {
        return ucwords(str_replace(['_', '-'], ' ', $name));
    }


}