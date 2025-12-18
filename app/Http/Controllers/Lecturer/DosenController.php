<?php

namespace App\Http\Controllers\Lecturer;

use App\Models\Course;
use App\Models\Schedule;
use App\Models\MkduCourse;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DosenController extends Controller
{
    public function teachingHistory(Request $request)
    {
        $lecturer = auth()->user()->lecturer;
        $academicYears = AcademicYear::orderBy('ta', 'desc')->get();
        $selectedTa = $request->input('academic_year_id', AcademicYear::where('status', 1)->value('id'));
        $selectedSemester = $request->input('semester');
        $query = Schedule::whereHas('lecturersInSchedule', function ($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id);
        })->with([
                    'schedulable' => function ($morphTo) {
                        $morphTo->morphWith([
                            Course::class => ['department'],
                            MkduCourse::class => [],
                        ]);
                    },
                    'kelas',
                    'room',
                    'academicYear'
                ]);

        if ($selectedTa) {
            $query->where('academic_year_id', $selectedTa);
        }

        if ($selectedSemester) {
            $query->whereHas('academicYear', function ($q) use ($selectedSemester) {
                $q->where('semester', $selectedSemester);
            });
        }

        $data = $query->get();

        return view('dosen.tridharma.teaching_history', compact('data', 'academicYears', 'selectedTa', 'selectedSemester'));
    }

    public function indexEdom()
    {
        $user = auth()->user();
        if (!$user->hasRole('dosen') && !$user->hasRole('ujm') && !$user->hasRole('dekan') && !$user->hasRole('kaprodi')) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat halaman ini.');
        }
        $lecturer = $user->lecturer;
        if (!$lecturer) {
            abort(403, 'Data dosen tidak ditemukan.');
        }
        $schedules = Schedule::whereHas('lecturersInSchedule', function ($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id);
        })
            ->with([
                'schedulable',
                'schedulable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Course::class => ['department.faculty'],
                        MkduCourse::class => [],
                    ]);
                },
                'responses.question.categoryName',
                'attendances.attendanceDetails'
            ])->get();

        $scheduleAverages = $schedules->mapWithKeys(function ($schedule) {
            $course = $schedule->course;
            if (!$course) {
                return [$schedule->id => ['ratings' => null, 'attendancePercentage' => null]];
            }
            if ($schedule->responses->isEmpty()) {
                return [$schedule->id => ['ratings' => null, 'attendancePercentage' => null]];
            }
            $ratings = [];
            foreach ($schedule->responses as $response) {
                $categoryValue = $response->question->categoryName->value ?? 'Uncategorized';
                $ratings[$categoryValue] = ($ratings[$categoryValue] ?? 0) + $response->rating;
            }
            $responseCount = $schedule->responses->count();
            $mappedRatings = collect($ratings)->map(fn($sum) => number_format($sum / $responseCount, 2))->all();
            $totalMeetings = 16;
            $totalPresent = $schedule->attendances->flatMap(function ($attendance) {
                return $attendance->attendanceDetails->where('status', 'Hadir');
            })->count();
            $totalPossibleAttendances = $totalMeetings * $schedule->attendances->count();
            $attendancePercentage = $totalPossibleAttendances > 0 ? number_format(($totalPresent / $totalPossibleAttendances) * 100, 2) : 0;

            return [$schedule->id => ['ratings' => $mappedRatings, 'attendancePercentage' => $attendancePercentage]];
        });
        $totalResponses = $schedules->sum(fn($schedule) => $schedule->responses->count());
        $totalCoursesEvaluated = $schedules->filter(fn($schedule) => $schedule->responses->count() > 0)->count();
        $overallAverageRating = $this->calculateOverallAverage($schedules);

        return view('dosen.edom.index', compact(
            'lecturer',
            'schedules',
            'scheduleAverages',
            'totalResponses',
            'totalCoursesEvaluated',
            'overallAverageRating'
        ));
    }

    public function scheduleDetail(Schedule $schedule)
    {
        $user = auth()->user();
        if (!$user->hasRole('dosen') && !$user->hasRole('ujm') && !$user->hasRole('dekan') && !$user->hasRole('kaprodi')) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat halaman ini.');
        }
        $lecturer = $user->lecturer;
        if (!$lecturer) {
            abort(403, 'Data dosen tidak ditemukan.');
        }
        if (
            !$schedule->lecturersInSchedule->contains(function ($lecturerInSchedule) use ($lecturer) {
                return $lecturerInSchedule->id === $lecturer->id;
            })
        ) {
            abort(403, 'Anda tidak mengajar mata kuliah ini.');
        }
        $schedule->load([
            'schedulable',
            'schedulable' => function ($morphTo) {
                $morphTo->morphWith([
                    Course::class => ['department.faculty'],
                    MkduCourse::class => [],
                ]);
            },
            'responses.question.categoryName',
            'attendances.attendanceDetails'
        ]);
        $averageRatings = [];
        if (!$schedule->responses->isEmpty()) {
            foreach ($schedule->responses as $response) {
                $categoryValue = $response->question->categoryName->value ?? 'Uncategorized';
                $averageRatings[$categoryValue] = ($averageRatings[$categoryValue] ?? 0) + $response->rating;
            }
            $responseCount = $schedule->responses->count();
            $averageRatings = collect($averageRatings)->map(fn($sum) => number_format($sum / $responseCount, 2));
        }
        $totalMeetings = 16;
        $totalPresent = $schedule->attendances->flatMap(function ($attendance) {
            return $attendance->attendanceDetails->where('status', 'Hadir');
        })->count();
        $attendanceCount = $schedule->attendances->count();
        $totalPossibleAttendances = $totalMeetings * $schedule->students->count();
        $divider = $attendanceCount > 0 ? $totalMeetings : 0;
        $attendancePercentage = ($divider > 0) ? number_format(($totalPresent / $divider) * 100, 2) : 0;
        $attendancePercentage = ($totalMeetings > 0 && $attendanceCount > 0) ? number_format(($totalPresent / ($totalMeetings * $schedule->students->count())) * 100, 2) : 0;
        $totalExpectedAttendances = $schedule->attendances->count() > 0 ? $schedule->attendances->count() * $schedule->kelas->students->count() : 0;
        $totalPresent = $schedule->attendances->flatMap->attendanceDetails->where('status', 'Hadir')->count();
        $totalStudentsInThisClass = $schedule->kelas->students->count();
        $totalPossibleAttendanceRecords = $totalMeetings * $totalStudentsInThisClass;
        $attendancePercentage = ($totalPossibleAttendanceRecords > 0)
            ? number_format(($totalPresent / $totalPossibleAttendanceRecords) * 100, 2)
            : 0;
        $responsesDetail = $schedule->responses->map(function ($response) {
            return [
                'student_id' => $response->student_id,
                'rating' => $response->rating,
                'comment' => $response->comment,
                'question' => $response->question->question_text ?? 'N/A',
                'category' => $response->question->categoryName->value ?? 'Uncategorized',
            ];
        });
        return view('dosen.edom.detail', compact('schedule', 'lecturer', 'averageRatings', 'responsesDetail', 'attendancePercentage'));
    }

    private function calculateOverallAverage($schedules)
    {
        $totalRatings = 0;
        $totalResponses = 0;
        foreach ($schedules as $schedule) {
            foreach ($schedule->responses as $response) {
                $totalRatings += $response->rating;
                $totalResponses++;
            }
        }
        return $totalResponses ? number_format($totalRatings / $totalResponses, 2) : 0;
    }

}
