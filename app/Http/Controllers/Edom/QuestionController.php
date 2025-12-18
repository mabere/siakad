<?php

namespace App\Http\Controllers\Edom;

use App\Models\Question;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\Questionnaire;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->roles->contains('name', 'admin')) {
            abort(403, 'Unauthorized access.');
        }

        $academicYearId = $request->input('academic_year_id', AcademicYear::where('status', 1)->first()->id);
        $academicYears = AcademicYear::orderBy('ta', 'desc')->get();

        $questionnaires = Questionnaire::with(['questions'])
            ->where('academic_year_id', $academicYearId)
            ->get();

        return view('backend.pertanyaan.index', compact('questionnaires', 'academicYearId', 'academicYears'));
    }

    public function show($id)
    {
        if (!Auth::user()->roles->contains('name', 'admin')) {
            abort(403, 'Unauthorized access.');
        }

        $questionnaire = Questionnaire::with([
            'questions' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }
        ])->findOrFail($id);

        return view('backend.pertanyaan.show', compact('questionnaire'));
    }

    public function create($questionnaire_id)
    {
        if (!Auth::user()->roles->contains('name', 'admin')) {
            abort(403, 'Unauthorized access.');
        }

        $questionnaire = Questionnaire::findOrFail($questionnaire_id);
        $questionTypes = [
            'likert' => 'Likert Scale',
            'multiple_choice' => 'Multiple Choice',
            'text' => 'Text Answer'
        ];

        return view('backend.pertanyaan.create', compact('questionnaire', 'questionTypes'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->roles->contains('name', 'admin')) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'question_text' => 'required|string|max:255',
            'type' => 'required',
            'options' => 'required_if:type,multiple_choice|nullable|string|max:255',
            'questionnaire_id' => 'required|exists:questionnaires,id',
        ]);

        try {
            Question::create([
                'question_text' => $validated['question_text'],
                'options' => $validated['options'] ?? null,
                'type' => $validated['type'],
                'questionnaire_id' => $validated['questionnaire_id'],
            ]);

            return redirect()
                ->route('admin.question.show', $validated['questionnaire_id'])
                ->with('success', 'Question added successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to add question. Please try again.');
        }
    }

    public function edit($id)
    {
        if (!Auth::user()->roles->contains('name', 'admin')) {
            abort(403, 'Unauthorized access.');
        }

        $question = Question::findOrFail($id);
        $questionnaire = $question->questionnaire;
        $questionTypes = [
            'likert' => 'Likert Scale',
            'multiple_choice' => 'Multiple Choice',
            'text' => 'Text Answer'
        ];

        return view('backend.pertanyaan.edit', compact('question', 'questionnaire', 'questionTypes'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->roles->contains('name', 'admin')) {
            Log::warning('Unauthorized access attempt', [
                'user_id' => Auth::id(),
                'role' => Auth::user()->role
            ]);
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'question_text' => 'required|string|max:255',
            'type' => 'required',
            'options' => 'required_if:type,multiple_choice|nullable|string|max:255',
        ]);

        try {
            $question = Question::findOrFail($id);
            $question->update([
                'question_text' => $validated['question_text'],
                'options' => $validated['options'] ?? $question->options,
                'type' => $validated['type'],
            ]);

            return redirect()
                ->route('admin.question.show', $question->questionnaire_id)
                ->with('success', 'Question updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update question', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to update question. Please try again.');
        }
    }

    public function destroy($id)
    {
        if (!Auth::user()->roles->contains('name', 'admin')) {
            abort(403, 'Unauthorized access.');
        }

        try {
            $question = Question::findOrFail($id);
            $questionnaireId = $question->questionnaire_id;

            // Check if there are any responses linked to this question
            if ($question->responses()->exists()) {
                return back()->with('error', 'Cannot delete question as it has responses.');
            }

            $question->delete();

            return redirect()
                ->route('admin.question.show', $questionnaireId)
                ->with('success', 'Question deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete question. Please try again.');
        }
    }
}