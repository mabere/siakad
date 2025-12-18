<?php

namespace App\Http\Controllers\Dekan;

use App\Models\Course;
use App\Models\Faculty;
use App\Models\Response;
use App\Models\Schedule;
use App\Models\Department;
use App\Models\EdomCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EdomDekanController extends Controller
{
    public function index()
    {
        $dekan = auth()->user();
        if (!$dekan->hasRole('dekan')) {
            abort(403, 'Hak akses dekan tidak ditemukan.');
        }
        // Pastikan relasi lecturer dan department dimuat
        $dekan->loadMissing('lecturer.department');
        $facultyId = $dekan->lecturer->department->faculty_id ?? null;
        if (!$facultyId) {
            abort(403, 'Fakultas tidak ditemukan untuk dekan ini.');
        }
        // Ambil semua schedules yang schedulable-nya adalah Course dan terkait dengan fakultas dekan
        $schedules = Schedule::whereHasMorph(
            'schedulable',
            [Course::class],
            function ($query) use ($facultyId) {
                $query->whereHas('department', function ($subQuery) use ($facultyId) {
                    $subQuery->where('faculty_id', $facultyId);
                });
            }
        )->with([
                    'schedulable',
                    'lecturersInSchedule',
                    'responses'
                ])
            ->get();
        // Jumlah respondens (total mahasiswa unik yang mengisi evaluasi di fakultas)
        $totalRespondents = Response::whereHas('schedule', function ($scheduleQuery) use ($facultyId) {
            $scheduleQuery->whereHasMorph(
                'schedulable',
                [Course::class],
                function ($courseQuery) use ($facultyId) {
                    $courseQuery->whereHas('department', function ($departmentQuery) use ($facultyId) {
                        $departmentQuery->where('faculty_id', $facultyId);
                    });
                }
            );
        })->distinct('student_id')->count();
        // Skor rata-rata dosen di fakultas
        $facultyAverageRating = $this->calculateFacultyAverageRating($schedules); // Pastikan metode ini menangani schedulable
        // Distribusi skor per departemen
        $departmentAverages = $this->calculateDepartmentAverages($facultyId);
        $departments = Department::where('faculty_id', $facultyId)->get(['id', 'nama']);
        return view('dekan.edom.index', compact(
            'facultyId',
            'totalRespondents',
            'facultyAverageRating',
            'departmentAverages',
            'schedules',
            'departments'
        ));
    }

    public function departmentReport(Department $department)
    {
        $user = Auth::user();
        if (!$user->roles()->where('name', 'dekan')->exists()) {
            abort(403, 'Hanya dekan yang dapat mengakses halaman ini.');
        }
        $lecturer = $user->lecturer;
        if (!$lecturer || !$lecturer->department) {
            abort(403, 'Data dosen atau departemen tidak ditemukan untuk user ini.');
        }

        $facultyId = $lecturer->department->faculty_id;
        if (!$facultyId) {
            abort(403, 'Fakultas tidak terkait dengan user ini.');
        }

        if ($department->faculty_id != $facultyId) {
            abort(403, 'Anda tidak memiliki akses ke departemen ini.');
        }

        $perPage = 5;
        $schedules = Schedule::whereHasMorph(
            'schedulable',
            [Course::class],
            function ($query) use ($department) {
                $query->where('department_id', $department->id);
            }
        )->with(['schedulable', 'lecturersInSchedule', 'responses.question'])->paginate($perPage);

        $averageRatings = $this->calculateAverages($schedules->items());
        $scheduleAverages = collect($schedules->items())->mapWithKeys(function ($schedule) {
            return [$schedule->id => $this->calculateAverages(collect([$schedule]))];
        });

        return view('dekan.edom.department_report', compact('department', 'schedules', 'averageRatings', 'scheduleAverages'));
    }

    private function calculateAverages($schedules)
    {
        $ratings = [];
        foreach ($schedules as $schedule) {
            if ($schedule->schedulable_type === Course::class) {
                foreach ($schedule->responses as $response) {
                    // Change ->category to ->categoryName
                    if ($response->question && $response->question->categoryName) { // Check for categoryName relation
                        $questionCategory = $response->question->categoryName->value; // Access value from categoryName relation
                        $ratings[$questionCategory] = ($ratings[$questionCategory] ?? 0) + $response->rating;
                    }
                }
            }
        }

        $allCategories = EdomCategory::pluck('value')->toArray();

        $finalAverages = collect($allCategories)->mapWithKeys(function ($categoryValue) use ($ratings, $schedules) {
            $sum = $ratings[$categoryValue] ?? 0;
            $count = 0;
            foreach ($schedules as $schedule) {
                if ($schedule->schedulable_type === Course::class) {
                    $count += $schedule->responses->filter(function ($response) use ($categoryValue) {
                        // Change ->category to ->categoryName
                        return $response->question && $response->question->categoryName && $response->question->categoryName->value === $categoryValue;
                    })->count();
                }
            }
            return [$categoryValue => $count ? number_format($sum / $count, 2) : 0];
        });

        return $finalAverages;
    }

    protected function calculateFacultyAverageRating($schedules)
    {
        $totalRatings = 0;
        $totalResponses = 0;

        foreach ($schedules as $schedule) {
            // Tambahkan pengecekan schedulable_type jika hanya Course yang dihitung
            if ($schedule->schedulable_type === Course::class) {
                foreach ($schedule->responses as $response) {
                    $totalRatings += $response->rating;
                    $totalResponses++;
                }
            }
        }

        return $totalResponses ? number_format($totalRatings / $totalResponses, 2) : 0;
    }

    protected function calculateDepartmentAverages($facultyId)
    {
        $departments = Department::where('faculty_id', $facultyId)->get();

        return $departments->mapWithKeys(function ($department) {
            // Perbaikan di sini: Menggunakan whereHasMorph
            $schedules = Schedule::whereHasMorph('schedulable', [Course::class], function ($query) use ($department) {
                $query->where('department_id', $department->id);
            })->with('responses')->get();

            $totalRatings = 0;
            $totalResponses = 0;

            foreach ($schedules as $schedule) {
                // Tambahkan pengecekan schedulable_type jika hanya Course yang dihitung
                if ($schedule->schedulable_type === Course::class) {
                    foreach ($schedule->responses as $response) {
                        $totalRatings += $response->rating;
                        $totalResponses++;
                    }
                }
            }

            $average = $totalResponses ? number_format($totalRatings / $totalResponses, 2) : 0;

            return [$department->nama => $average];
        });
    }

    protected function calculateFacultyAverages(Faculty $faculty)
    {
        $schedules = Schedule::whereHasMorph('schedulable', [Course::class], function ($query) use ($faculty) {
            $query->whereHas('department', function ($subQuery) use ($faculty) {
                $subQuery->where('faculty_id', $faculty->id);
            });
        })->with('responses')->get();

        return $this->calculateAverages($schedules);
    }

    // Helper untuk warna progress bar
    function getProgressColor($avg)
    {
        if ($avg >= 4)
            return 'success';
        if ($avg >= 3)
            return 'warning';
        return 'danger';
    }

}