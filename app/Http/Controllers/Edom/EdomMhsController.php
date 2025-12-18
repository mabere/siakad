<?php

namespace App\Http\Controllers\Edom;

use Carbon\Carbon;
use App\Models\Course;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Question;
use App\Models\Response;
use App\Models\Schedule;
use App\Models\MkduCourse;
use App\Models\EdomSetting;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\Questionnaire;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EdomMhsController extends Controller
{
    public function index()
    {
        $settings = Setting::where('key', 'edom_active')->first();
        $isEdomActive = $settings ? $settings->value == 1 : false;
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan untuk pengguna ini.');
        }

        if (!$isEdomActive) {
            return view('edom.mhs.index', [
                'schedules' => collect(),
                'academicYear' => null,
                'isEdomActive' => false,
                'questionnaire' => null
            ]);
        }

        $academicYear = AcademicYear::where('status', 1)->firstOrFail();
        $questionnaire = Questionnaire::where('is_active', true)
            ->where('academic_year_id', $academicYear->id)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with('questions')
            ->first();

        $schedules = Schedule::with([
            'schedulable',
            'lecturersInSchedule',
            'responses' => fn($query) => $query->where('student_id', $student->id)
        ])
            ->where('academic_year_id', $academicYear->id)
            ->whereHas('studyPlans', fn($query) => $query->where('student_id', $student->id))
            ->where(function ($query) use ($student) {
                $query->whereHasMorph(
                    'schedulable',
                    [Course::class],
                    function ($morphQuery) use ($student) {
                        $morphQuery->where('department_id', $student->department_id);
                    }
                )->orWhere('schedulable_type', MkduCourse::class);
            })
            ->get()
            ->map(function ($schedule) use ($questionnaire) {
                $schedule->hasFilledEdom = $schedule->responses->where('questionnaire_id', $questionnaire?->id)->count() > 0;
                return $schedule;
            });

        return view('edom.mhs.index', compact(
            'schedules',
            'academicYear',
            'isEdomActive',
            'questionnaire'
        ));
    }

    public function create($scheduleId)
    {
        // <<< PERBAIKAN: Ganti 'course' menjadi 'schedulable'
        $schedule = Schedule::with(['schedulable', 'lecturersInSchedule'])->findOrFail($scheduleId);
        $academicYear = AcademicYear::where('status', 1)->first();
        $student = Student::where('user_id', auth()->id())->first();

        if (!$student) {
            return redirect()->route('student.edom.index') // Perhatikan rute 'student.edom.index'
                ->with('error', 'Data mahasiswa tidak ditemukan');
        }

        // Pastikan questionnaire_id juga digunakan dalam pengecekan ini jika kuesioner bisa berbeda per tahun/periode
        $questionnaire = Questionnaire::where('type', 'EDOM')
            ->where('status', 'ACTIVE')
            ->where('academic_year_id', $academicYear->id) // Menambahkan filter academic_year_id agar spesifik
            ->first();

        // Tambahkan pengecekan jika kuesioner tidak ditemukan atau tidak aktif
        if (!$questionnaire) {
            return redirect()->route('student.edom.index')
                ->with('error', 'Kuesioner EDOM belum tersedia atau tidak aktif untuk periode ini.');
        }

        $hasFilledEdom = Response::where([
            'schedule_id' => $scheduleId,
            'student_id' => $student->id,
            'academic_year_id' => $academicYear->id,
            'questionnaire_id' => $questionnaire->id // Menambahkan filter questionnaire_id
        ])->exists();

        if ($hasFilledEdom) {
            return redirect()->route('student.edom.index') // Perhatikan rute 'student.edom.index'
                ->with('error', 'Anda sudah mengisi EDOM untuk mata kuliah ini sebelumnya');
        }

        $questions = Question::where('questionnaire_id', $questionnaire->id)
            ->orderBy('category')
            ->get()
            ->groupBy('category')
            ->map(function ($items) {
                return $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'question_text' => $item->question_text,
                        'category' => $item->category,
                        'type' => $item->type,
                        'weight' => $item->weight
                    ];
                });
            });

        return view('edom.mhs.create', compact('schedule', 'questions'));
    }

    public function store(Request $request, $scheduleId)
    {
        $validated = $request->validate([
            'responses' => 'required|array',
            'responses.*.rating' => 'required|integer|between:1,5',
            'comments' => 'nullable|string|max:1000'
        ]);

        $schedule = Schedule::findOrFail($scheduleId);
        $questionnaire = Questionnaire::where('type', 'EDOM')
            ->where('status', 'ACTIVE')
            ->first(); // Perlu diingat, ini mungkin perlu difilter berdasarkan academic_year_id juga
        $academicYear = AcademicYear::where('status', 1)->first();
        $student = Student::where('user_id', auth()->id())->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan'
            ], 403);
        }
        if (!$questionnaire) {
            return response()->json([
                'success' => false,
                'message' => 'Kuesioner EDOM belum tersedia'
            ], 422);
        }
        if (!$academicYear) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun Akademik aktif tidak ditemukan'
            ], 422);
        }

        $existingResponses = Response::where([
            'schedule_id' => $scheduleId,
            'student_id' => $student->id,
            'academic_year_id' => $academicYear->id,
            'questionnaire_id' => $questionnaire->id
        ])->exists();

        if ($existingResponses) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mengisi EDOM untuk mata kuliah ini sebelumnya'
            ], 422);
        }

        try {
            DB::beginTransaction();

            foreach ($validated['responses'] as $questionId => $data) {
                $responseData = [
                    'schedule_id' => $scheduleId,
                    'questionnaire_id' => $questionnaire->id,
                    'academic_year_id' => $academicYear->id,
                    'student_id' => $student->id,
                    'question_id' => $questionId,
                    'rating' => $data['rating']
                ];

                // Hanya tambahkan 'comment' jika ada di input
                if (isset($validated['comments'])) {
                    $responseData['comment'] = $validated['comments'];
                }

                Response::create($responseData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Terima kasih! EDOM berhasil disimpan',
                'redirect' => route('student.edom.index')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('EDOM Save Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function adminIndex() // Manajemen by Admin
    {
        $academicYear = AcademicYear::where('status', 1)->first();
        $schedules = Schedule::with([
            'schedulable',
            'lecturersInSchedule',
            'responses' => function ($query) use ($academicYear) {
                $query->where('academic_year_id', $academicYear->id)
                    ->where('student_id', function ($subquery) {
                        $subquery->select('id')
                            ->from('students')
                            ->where('user_id', auth()->id())
                            ->limit(1);
                    });
            }
        ])->get();

        return view('edom.admin.index', compact('schedules'));
    }

    public function report($scheduleId)
    {
        // Hanya admin dan dosen yang bisa akses
        if (!auth()->user()->hasAnyRole(['admin', 'dosen'])) {
            return redirect()->route('student.edom.index')
                ->with('error', 'Anda tidak memiliki akses ke halaman laporan EDOM');
        }

        $schedule = Schedule::with(['schedulable', 'lecturersInSchedule'])->findOrFail($scheduleId);
        $academicYear = AcademicYear::where('status', 1)->first();

        // Kategori EDOM
        $categoryNames = [
            'PERENCANAAN' => 'Perencanaan Pembelajaran',
            'PELAKSANAAN' => 'Pelaksanaan Pembelajaran',
            'EVALUASI' => 'Evaluasi Pembelajaran',
            'KEPRIBADIAN' => 'Kepribadian Dosen'
        ];

        // Ambil semua responses untuk schedule ini
        $responses = Response::where([
            'schedule_id' => $scheduleId,
            'academic_year_id' => $academicYear->id
        ])->with('question')->get();

        // Hitung total responden unik
        $totalResponden = $responses->pluck('student_id')->unique()->count();

        // Inisialisasi array results dengan nilai default
        $results = array_fill_keys(array_keys($categoryNames), ['total' => 0, 'count' => 0, 'average' => 0]);
        $detailResults = [];

        // Hitung rata-rata
        foreach ($responses as $response) {
            $category = $response->question->category;
            $questionId = $response->question_id;

            // Update category totals
            $results[$category]['total'] += $response->rating;
            $results[$category]['count']++;

            // Update question details
            if (!isset($detailResults[$questionId])) {
                $detailResults[$questionId] = [
                    'question_text' => $response->question->question_text,
                    'category' => $category,
                    'total' => 0,
                    'count' => 0,
                    'average' => 0
                ];
            }
            $detailResults[$questionId]['total'] += $response->rating;
            $detailResults[$questionId]['count']++;
        }

        // Hitung rata-rata untuk setiap kategori dan pertanyaan
        foreach ($results as &$data) {
            $data['average'] = $data['count'] > 0 ? round($data['total'] / $data['count'], 2) : 0;
        }

        foreach ($detailResults as &$data) {
            $data['average'] = $data['count'] > 0 ? round($data['total'] / $data['count'], 2) : 0;
        }

        return view('edom.admin.report', compact(
            'schedule',
            'academicYear',
            'categoryNames',
            'results',
            'detailResults',
            'totalResponden'
        ));
    }


}
