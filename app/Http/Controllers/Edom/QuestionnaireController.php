<?php

namespace App\Http\Controllers\Edom;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\Response;
use App\Models\Department;
use App\Models\AcademicYear;
use App\Models\Survey;
use Illuminate\Http\Request;
use App\Models\Questionnaire;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\YearFrac;

class QuestionnaireController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role == 3) {
            $user = Auth::user();
            $courses = $user->courses;
            $activeQuestionnaires = Questionnaire::with('questions')->where('is_active', true)->get();
        } else {
            $activeQuestionnaires = Questionnaire::with('questions')->where('is_active', true)->get();
        }

        return view('admin.survey.index', compact('activeQuestionnaires'));
    }

    public function show(Questionnaire $questionnaire)
    {
        $user = Auth::user();
        $activeYear = AcademicYear::where('status', true)->first();

        $hasResponded = $user->responses()
            ->where('questionnaire_id', $questionnaire->id)
            ->whereHas('questionnaire', function ($query) use ($activeYear) {
                $query->where('academic_year_id', $activeYear->id);
            })
            ->exists();

        if ($hasResponded) {
            // Throw a ValidationException to leverage Laravel's error handling
            throw ValidationException::withMessages([
                'questionnaire' => 'You have already submitted a response for this questionnaire.'
            ]);
        }

        $questions = $questionnaire->questions->groupBy('type');
        return view('admin.survey.shows', compact('questionnaire', 'questions'))->with('error', 'You have already submitted a response for this questionnaire in the current academic year.');
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'questionnaire_id' => 'required|exists:questionnaires,id',
                'ratings' => 'required|array',
                'ratings.*' => 'required|integer|between:1,5',
            ]);

            // Ambil tahun akademik aktif
            $activeYear = AcademicYear::where('status', 1)->first();
            if (!$activeYear) {
                \Log::error('Active academic year not found');
                return back()->with('error', 'Tahun akademik aktif tidak ditemukan');
            }

            // Ambil data yang diperlukan
            $student = Auth::user()->student;
            if (!$student) {
                \Log::error('Student data not found');
                return back()->with('error', 'Data mahasiswa tidak ditemukan');
            }

            // Siapkan data untuk bulk insert
            $responses = [];
            $now = now();

            foreach ($validated['ratings'] as $questionId => $rating) {
                $responses[] = [
                    'student_id' => $student->id,
                    'questionnaire_id' => $validated['questionnaire_id'],
                    'question_id' => $questionId,
                    'rating' => $rating,
                    'academic_year_id' => $activeYear->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Bulk insert dengan transaction
            DB::beginTransaction();
            try {
                Response::insert($responses);
                DB::commit();

                return redirect()->route('student.questionnaire.index')
                    ->with('success', 'Survei berhasil disubmit!');
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Database error:', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            \Log::error('Survey submission error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function create()
    {
        if (auth()->user()->role !== 1) {
            abort(403);
        }

        $activeYear = AcademicYear::where('status', true)->first();
        return view('admin.questionnaire.create', compact('activeYear'));
    }

    public function edit(Questionnaire $questionnaire)
    {
        if (auth()->user()->role !== 1) {
            abort(403);
        }

        return view('admin.questionnaire.edit', compact('questionnaire'));
    }

    public function update(Request $request, Questionnaire $questionnaire)
    {
        if (auth()->user()->role !== 1) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'is_active' => 'boolean'
        ]);

        $questionnaire->update($validated);

        return redirect()->route('admin.questionnaire.index')
            ->with('success', 'Questionnaire updated successfully');
    }

    public function destroy(Questionnaire $questionnaire)
    {
        if (auth()->user()->role !== 1) {
            abort(403);
        }

        $questionnaire->delete();

        return redirect()->route('admin.questionnaire.index')
            ->with('success', 'Questionnaire deleted successfully');
    }

    public function surveyResults($id)
    {
        try {
            DB::enableQueryLog();

            // Get questionnaire with questions and responses
            $questionnaire = Questionnaire::with(['questions', 'responses'])->findOrFail($id);

            // Get respondent count
            $respondentCount = Response::where('questionnaire_id', $id)
                ->distinct('student_id')
                ->count('student_id');

            // Get all responses with questions
            $responses = DB::table('responses as r')
                ->join('questions as q', 'r.question_id', '=', 'q.id')
                ->where('r.questionnaire_id', $id)
                ->whereNotNull('r.rating')
                ->select(
                    'r.question_id',
                    'r.rating',
                    'q.question_text',
                    'q.type'
                )
                ->get();

            // Jika tidak ada respon, tampilkan data kosong
            if ($responses->isEmpty()) {
                return view('survey.hasilsurvei', [
                    'questionnaire' => $questionnaire,
                    'respondentCount' => 0,
                    'chartDataSoal' => [
                        'labels' => [],
                        'datasets' => [
                            [
                                'label' => 'Rata-rata Respon',
                                'data' => [],
                                'backgroundColor' => 'rgba(75, 192, 192, 0.6)'
                            ]
                        ]
                    ],
                    'chartDataTypeSoal' => [
                        'labels' => [],
                        'datasets' => [
                            [
                                'label' => 'Rata-rata per Kategori',
                                'data' => [],
                                'backgroundColor' => []
                            ]
                        ]
                    ],
                    'genderChartData' => [
                        'labels' => ['Tidak Ada Data'],
                        'datasets' => [
                            [
                                'label' => 'Distribusi Gender',
                                'data' => [0],
                                'backgroundColor' => ['rgba(200, 200, 200, 0.6)']
                            ]
                        ]
                    ]
                ]);
            }

            // Process data untuk charts
            $chartDataSoal = $this->processQuestionChart($responses);
            $chartDataTypeSoal = $this->processTypeChart($responses);
            $genderChartData = $this->processGenderChart($id);

            return view('survey.hasilsurvei', compact(
                'questionnaire',
                'respondentCount',
                'chartDataSoal',
                'chartDataTypeSoal',
                'genderChartData'
            ));

        } catch (\Exception $e) {
            \Log::error('Survey Results Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'queries' => DB::getQueryLog()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses hasil survei: ' . $e->getMessage());
        }
    }

    private function processQuestionChart($responses)
    {
        // Urutkan responses berdasarkan question_id
        $questionData = $responses->sortBy('question_id')
            ->groupBy('question_id')
            ->values() // Reset index array setelah sort
            ->map(function ($group, $index) {
                return [
                    'text' => ($index + 1), // Index dimulai dari 0, tambah 1
                    'question_text' => $group->first()->question_text,
                    'average' => round($group->avg('rating'), 2)
                ];
            });

        $data = $questionData->pluck('average')->values();

        return [
            'labels' => $questionData->pluck('text')->values(),
            'tooltips' => $questionData->pluck('question_text')->values(),
            'datasets' => [
                [
                    'label' => 'Rata-rata Respon',
                    'data' => $data,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.6)',
                    'average' => $data->avg()
                ]
            ]
        ];
    }

    private function processTypeChart($responses)
    {
        $typeData = $responses->groupBy('type')->map(function ($group) {
            return round($group->avg('rating'), 2);
        });

        return [
            'labels' => $typeData->keys(),
            'datasets' => [
                [
                    'label' => 'Rata-rata per Kategori',
                    'data' => $typeData->values(),
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)'
                    ]
                ]
            ]
        ];
    }

    private function processGenderChart($questionnaireId)
    {
        $genderData = DB::table('responses as r')
            ->join('students as s', 'r.student_id', '=', 's.id')
            ->where('r.questionnaire_id', $questionnaireId)
            ->select('s.gender', DB::raw('COUNT(DISTINCT r.student_id) as count'))
            ->groupBy('s.gender')
            ->get();

        return [
            'labels' => $genderData->pluck('gender'),
            'datasets' => [
                [
                    'label' => 'Distribusi Gender',
                    'data' => $genderData->pluck('count'),
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.6)',  // Pink for female
                        'rgba(54, 162, 235, 0.6)'   // Blue for male
                    ]
                ]
            ]
        ];
    }
}