<?php

namespace App\Http\Controllers\Edom;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\Questionnaire;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SurveyController extends Controller
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

        return view('survey.index', compact('activeQuestionnaires'));
    }

    public function show(Questionnaire $questionnaire)
    {
        $questions = $questionnaire->questions->groupBy('type');
        return view('survey.shows', compact('questionnaire', 'questions'));
    }

    public function surveyReport($id)
    {
        $questionnaire = Questionnaire::findOrFail($id);
        $activeYear = AcademicYear::where('status', true)->first();
        $responses = $questionnaire->responses()
            ->whereHas('questionnaire', function ($query) use ($activeYear) {
                $query->where('academic_year_id', $activeYear->id);
            })->get();
        $averageRatings = $responses->groupBy('question_id')
            ->map(function ($group) {
                return $group->avg('rating');
            });
        $labels = $responses->pluck('question.question_text')->unique()->values()->toArray();
        $alignedAverageRatings = collect($labels)->map(function ($label) use ($averageRatings, $questionnaire) {
            $questionId = $questionnaire->questions->firstWhere('question_text', $label)->id;
            return $averageRatings->get($questionId, 0);
        });
        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Average Rating',
                    'data' => $alignedAverageRatings->toArray(),
                ],
            ],
        ];
        return view('survey.report', compact('questionnaire', 'chartData'));
    }

    public function surveyResults($id)
    {
        try {
            $questionnaire = Questionnaire::findOrFail($id);
            $activeYear = AcademicYear::where('status', true)->firstOrFail();

            // Fetch responses for the active academic year (consider extracting this into a scope or method)
            $responses = $questionnaire->responses()
                ->whereHas('questionnaire', function ($query) use ($activeYear) {
                    $query->where('academic_year_id', $activeYear->id);
                })
                ->get();

            // Check for responses
            if ($responses->isEmpty()) {
                return view('survey.hasilsurvei', compact('questionnaire'))
                    ->with('info', 'No responses found for this questionnaire in the current academic year.');
            }

            // Calculate average ratings per question
            $averageRatingsPerQuestion = $responses->groupBy('question_id')
                ->map(function ($group) {
                    return $group->avg('rating');
                });

            // Calculate average ratings per category
            $averageRatingsPerCategory = $responses->groupBy('question.type')
                ->map(function ($group) {
                    return $group->avg('rating');
                });

            // Prepare data for the first chart (all questions)
            $questionLabels = $responses->pluck('question.question_text')->unique()->values()->toArray();
            $alignedAverageRatings = collect($questionLabels)->map(function ($label) use ($averageRatingsPerQuestion, $questionnaire) {
                $questionId = $questionnaire->questions->firstWhere('question_text', $label)->id;
                return $averageRatingsPerQuestion->get($questionId, 0);
            });

            $chartDataSoal = [
                'labels' => $questionLabels,
                'datasets' => [
                    ['label' => 'Rerata Hasil Survei', 'data' => $alignedAverageRatings->toArray()],
                ],
            ];

            // Prepare data for the second chart (by category)
            $categoryLabels = $averageRatingsPerCategory->keys()->map(function ($type) {
                return $type;
            })->toArray();

            $chartDataTypeSoal = [
                'labels' => $categoryLabels,
                'datasets' => [
                    [
                        'label' => 'Rerata Hasil Survei',
                        'data' => $averageRatingsPerCategory->values()->toArray(),
                        'backgroundColor' => [
                            'red',
                            'yellow',
                            'green',
                            'blue',
                            'violet'
                        ],
                        'borderColor' => 'rgba(54, 162, 235, 1)',
                        'borderWidth' => 1
                    ]
                ]
            ];

            return view('survey.hasilsurvei', compact('questionnaire', 'chartDataSoal', 'chartDataTypeSoal'));

        } catch (\Exception $e) {
            // Handle exceptions (e.g., log the error, display an error message to the user)
            Log::error('Error generating survey report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while generating the report. Please try again later.');
        }
    }
}