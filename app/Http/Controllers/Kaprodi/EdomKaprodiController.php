<?php
namespace App\Http\Controllers\Kaprodi;

use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Response;
use App\Models\Schedule;
use App\Models\Department;
use App\Models\MkduCourse;
use App\Exports\EdomExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;

class EdomKaprodiController extends Controller
{
    public function index(Request $request)
    {
        $kaprodi = auth()->user();

        if (!$kaprodi->hasRole('kaprodi')) {
            abort(403, 'Hak akses kaprodi tidak ditemukan.');
        }

        $lecturer = $kaprodi->lecturer;

        if (!$lecturer) {
            abort(403, 'Data kaprodi tidak ditemukan.');
        }

        $department = $lecturer->department;
        $departmentId = $department->id ?? null;

        if (!$department) {
            abort(403, 'Departemen tidak ditemukan.');
        }

        $queryKaprodiCourses = function ($query) use ($departmentId) {
            $query->where(function ($subQuery) use ($departmentId) {
                // Kondisi 1: Jadwal mata kuliah prodi dari departemen yang sama
                $subQuery->whereHasMorph('schedulable', [Course::class], function ($morphQuery) use ($departmentId) {
                    $morphQuery->where('department_id', $departmentId);
                });
            })->orWhere(function ($subQuery) use ($departmentId) {
                // Kondisi 2: Jadwal mata kuliah MKDU yang diambil oleh mahasiswa dari departemen ini
                $subQuery->whereHasMorph('schedulable', [MkduCourse::class])
                    ->whereHas('studyPlans.student', function ($studentQuery) use ($departmentId) {
                        $studentQuery->where('department_id', $departmentId);
                    });
            });
        };
        // Hitung statistik total
        $totalSchedules = Schedule::where($queryKaprodiCourses)->count();

        $evaluatedSchedules = Schedule::where($queryKaprodiCourses)->has('responses')->count();

        $nonEvaluatedSchedules = $totalSchedules - $evaluatedSchedules;

        // Ambil data dengan pagination
        $schedules = Schedule::where($queryKaprodiCourses)
            ->with([
                'schedulable' => fn($morphTo) => $morphTo->morphWith([
                    Course::class,
                    MkduCourse::class,
                ]),
                'lecturersInSchedule',
                'responses.question.categoryName',
                'academicYear',
                'studyPlans.student',
            ])
            ->filter($request->all())
            ->paginate(5)
            ->appends($request->query());

        // Hitung rata-rata per schedule
        $scheduleAverages = $schedules->mapWithKeys(function ($schedule) {
            if ($schedule->responses->isEmpty()) {
                return [$schedule->id => null];
            }

            $ratings = [];
            foreach ($schedule->responses as $response) {
                $categoryValue = $response->question->categoryName->value ?? 'Uncategorized';
                $ratings[$categoryValue] = ($ratings[$categoryValue] ?? 0) + $response->rating;
            }

            $responseCount = $schedule->responses->count();
            return [$schedule->id => collect($ratings)->map(fn($sum) => number_format($sum / $responseCount, 2))];
        });

        return view('kaprodi.edom.index', compact(
            'department',
            'totalSchedules',
            'evaluatedSchedules',
            'nonEvaluatedSchedules',
            'schedules',
            'scheduleAverages'
        ));
    }

    public function reports(Request $request)
    {
        $kaprodi = auth()->user();

        if (!$kaprodi->hasRole('kaprodi')) {
            abort(403, 'Hak akses kaprodi tidak ditemukan.');
        }

        $lecturer = $kaprodi->lecturer;

        if (!$lecturer) {
            abort(403, 'Data kaprodi tidak ditemukan.');
        }

        $department = $lecturer->department;

        if (!$department) {
            abort(403, 'Departemen tidak ditemukan.');
        }

        $queryKaprodiCourses = function ($query) use ($department) {
            $query->where(function ($subQuery) use ($department) {
                $subQuery->whereHasMorph('schedulable', [Course::class], function ($morphQuery) use ($department) {
                    $morphQuery->where('department_id', $department->id);
                });
            })->orWhere(function ($subQuery) use ($department) {
                $subQuery->whereHasMorph('schedulable', [MkduCourse::class])
                    ->whereHas('studyPlans.student', function ($studentQuery) use ($department) {
                        $studentQuery->where('department_id', $department->id);
                    });
            });
        };

        $schedules = Schedule::where($queryKaprodiCourses)
            ->with([
                'schedulable' => fn($morphTo) => $morphTo->morphWith([
                    Course::class,
                    MkduCourse::class,
                ]),
                'lecturersInSchedule',
                'responses.question.categoryName'
            ])
            ->paginate(5);

        $averageRatings = $this->calculateAverages($schedules->items());

        $scheduleAverages = $schedules->mapWithKeys(function ($schedule) {
            if ($schedule->responses->isEmpty()) {
                return [$schedule->id => null];
            }

            $ratings = [];
            foreach ($schedule->responses as $response) {
                $categoryValue = $response->question->categoryName->value ?? 'Uncategorized';
                $ratings[$categoryValue] = ($ratings[$categoryValue] ?? 0) + $response->rating;
            }

            $responseCount = $schedule->responses->count();
            return [$schedule->id => collect($ratings)->map(fn($sum) => number_format($sum / $responseCount, 2))];
        });

        return view('kaprodi.edom.reports', compact('department', 'schedules', 'averageRatings', 'scheduleAverages'));
    }

    // public function export(Request $request)
    // {
    //     $filters = $request->only(['search', 'year', 'semester']);

    //     if ($request->type === 'excel') {
    //         return Excel::download(new EdomExport($filters), 'edom-export.xlsx');
    //     }

    //     if ($request->type === 'pdf') {
    //         $data = Schedule::with(['course', 'lecturersInSchedule', 'responses'])
    //             ->filter($filters)
    //             ->get();

    //         $pdf = PDF::loadView('kaprodi.edom.pdf', compact('data'));
    //         return $pdf->download('edom-export.pdf');
    //     }
    // }

    public function scheduleDetail(Schedule $schedule)
    {
        $kaprodi = auth()->user();

        if (!$kaprodi->hasRole('kaprodi')) {
            abort(403, 'Hak akses kaprodi tidak ditemukan.');
        }

        $lecturer = $kaprodi->lecturer;

        if (!$lecturer) {
            abort(403, 'Data kaprodi tidak ditemukan.');
        }

        $department = $lecturer->department;
        $departmentId = $department->id ?? null;

        if (!$department) {
            abort(403, 'Departemen tidak ditemukan.');
        }

        // --- PERBAIKAN DI SINI: Validasi dan Eager Loading dalam satu langkah ---
        // Logika query polimorfik yang sama untuk memastikan jadwal ini ada di bawah wewenang kaprodi
        $authorizedSchedule = Schedule::where('id', $schedule->id)
            ->where(function ($query) use ($departmentId) {
                // Kondisi 1: Jadwal mata kuliah prodi dari departemen yang sama
                $query->whereHasMorph('schedulable', [Course::class], function ($morphQuery) use ($departmentId) {
                    $morphQuery->where('department_id', $departmentId);
                });
            })->orWhere(function ($query) use ($departmentId) {
                // Kondisi 2: Jadwal mata kuliah MKDU yang diambil oleh mahasiswa dari departemen ini
                $query->whereHasMorph('schedulable', [MkduCourse::class])
                    ->whereHas('studyPlans.student', function ($studentQuery) use ($departmentId) {
                    $studentQuery->where('department_id', $departmentId);
                });
            })
            ->with([
                // Eager loading polimorfik untuk schedulable
                'schedulable' => fn($morphTo) => $morphTo->morphWith([
                    Course::class,
                    MkduCourse::class,
                ]),
                'lecturersInSchedule',
                'responses.question.categoryName'
            ])
            ->firstOrFail(); // Akan throw 404 jika tidak ditemukan (tidak terotorisasi)

        // Gunakan schedule yang sudah divalidasi dan di-load
        $schedule = $authorizedSchedule;
        // --- AKHIR PERBAIKAN ---

        // Hitung rata-rata per kategori untuk jadwal ini
        $averageRatings = [];
        if (!$schedule->responses->isEmpty()) {
            foreach ($schedule->responses as $response) {
                $categoryValue = $response->question->categoryName->value ?? 'Uncategorized';
                $averageRatings[$categoryValue] = ($averageRatings[$categoryValue] ?? 0) + $response->rating;
            }

            $responseCount = $schedule->responses->count();
            $averageRatings = collect($averageRatings)->map(fn($sum) => number_format($sum / $responseCount, 2));
        }

        // Detail respons individu
        $responsesDetail = $schedule->responses->map(function ($response) {
            return [
                'student_id' => $response->student_id,
                'rating' => $response->rating,
                'comment' => $response->comment,
                'question' => $response->question->question_text ?? 'Uncategorized',
                'category' => $response->question->categoryName->value ?? 'Uncategorized',
            ];
        });

        return view('kaprodi.edom.report_detail', compact('schedule', 'department', 'averageRatings', 'responsesDetail'));
    }

    private function calculateAverages($schedules)
    {
        // Konversi ke Collection jika berupa array
        $schedules = is_array($schedules) ? collect($schedules) : $schedules;

        $ratings = [];
        $responseCounts = [];

        foreach ($schedules as $schedule) {
            foreach ($schedule->responses as $response) {
                $categoryValue = $response->question->categoryName->value ?? 'Uncategorized';

                // Hitung total rating per kategori
                $ratings[$categoryValue] = ($ratings[$categoryValue] ?? 0) + $response->rating;

                // Hitung jumlah response per kategori
                $responseCounts[$categoryValue] = ($responseCounts[$categoryValue] ?? 0) + 1;
            }
        }

        // Hitung rata-rata per kategori
        $averages = [];
        foreach ($ratings as $category => $totalRating) {
            $count = $responseCounts[$category] ?? 0;
            $averages[$category] = $count ? number_format($totalRating / $count, 2) : 0;
        }

        return $averages;
    }

    public function export(Request $request)
    {
        $kaprodi = auth()->user();
        if (!$kaprodi->hasRole('kaprodi')) {
            abort(403, 'Hak akses kaprodi tidak ditemukan.');
        }
        $department = $kaprodi->lecturer->department;
        $departmentId = $department->id ?? null;
        if (!$department) {
            abort(403, 'Departemen tidak ditemukan.');
        }
        $filters = $request->only(['search', 'year', 'semester']);
        if ($request->type === 'excel') {
            return Excel::download(new EdomExport($filters, $departmentId), 'edom-export.xlsx');
        }
        if ($request->type === 'pdf') {
            $queryKaprodiCourses = function ($query) use ($departmentId) {
                $query->where(function ($subQuery) use ($departmentId) {
                    $subQuery->whereHasMorph('schedulable', [Course::class], function ($morphQuery) use ($departmentId) {
                        $morphQuery->where('department_id', $departmentId);
                    });
                })->orWhere(function ($subQuery) use ($departmentId) {
                    $subQuery->whereHasMorph('schedulable', [MkduCourse::class])
                        ->whereHas('studyPlans.student', function ($studentQuery) use ($departmentId) {
                            $studentQuery->where('department_id', $departmentId);
                        });
                });
            };
            $data = Schedule::where($queryKaprodiCourses)
                ->with([
                    'schedulable' => fn($morphTo) => $morphTo->morphWith([
                        Course::class,
                        MkduCourse::class,
                    ]),
                    'lecturersInSchedule',
                    'responses.question.categoryName',
                    'academicYear'
                ])
                ->filter($filters)
                ->get();
            $kaprodiName = $kaprodi->name;
            $pdf = PDF::loadView('kaprodi.edom.pdf', compact('data', 'department', 'kaprodiName'));
            return $pdf->stream('edom-export.pdf');
        }
    }

}