<?php

namespace App\Http\Controllers\Edom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Questionnaire;
use App\Models\Question;
use App\Models\Response;
use App\Models\EdomCategory;

class EdomQuestionController extends Controller
{
    public function index(Questionnaire $questionnaire)
    {
        $questions = $questionnaire->questions()->with('categoryName')->get();
        $categories = EdomCategory::pluck('value', 'id');
        return view('admin.edom.questions.index', compact('questionnaire', 'questions', 'categories'));
    }

    public function create(Questionnaire $questionnaire)
    {
        $categories = EdomCategory::pluck('value', 'id');
        return view('admin.edom.questions.create', compact('questionnaire', 'categories'));
    }

    public function store(Request $request, Questionnaire $questionnaire)
    {
        $validated = $request->validate([
            'category' => 'required|exists:edom_categories,id',
            'question_text' => 'required|string|max:1000',
            'weight' => 'required|numeric|between:1,5',
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'category' => $validated['category'],
            'question_text' => $validated['question_text'],
            'type' => 'RATING',
            'weight' => $validated['weight'],
        ]);

        return redirect()->route('admin.edom.questions.index', $questionnaire)
            ->with('success', 'Pertanyaan EDOM berhasil ditambahkan.');
    }

    public function edit(Question $question)
    {
        $categories = EdomCategory::pluck('value', 'id');
        return view('admin.edom.questions.edit', compact('question', 'categories'));
    }

    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'category' => 'required|exists:edom_categories,id',
            'question_text' => 'required|string|max:1000',
            'weight' => 'required|numeric|between:1,5',
        ]);

        $question->update($validated);

        return redirect()->route('admin.edom.questions.index', $question->questionnaire)
            ->with('success', 'Pertanyaan EDOM berhasil diperbarui.');
    }

    public function destroy(Question $question)
    {
        if (Response::where('question_id', $question->id)->exists()) {
            return redirect()->back()->with('error', 'Pertanyaan ini tidak dapat dihapus karena masih digunakan di respons.');
        }

        $question->delete();

        return redirect()->route('admin.edom.questions.index', $question->questionnaire)
            ->with('success', 'Pertanyaan EDOM berhasil dihapus.');
    }
}