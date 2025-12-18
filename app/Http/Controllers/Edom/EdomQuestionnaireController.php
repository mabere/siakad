<?php

namespace App\Http\Controllers\Edom;

use App\Http\Controllers\Controller;
use App\Models\Questionnaire;
use App\Models\Question;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EdomQuestionnaireController extends Controller
{
    public function index()
    {
        $questionnaires = Questionnaire::where('type', 'EDOM')
            ->withCount('questions')
            ->latest()
            ->get();

        $activeQuestionnaire = Questionnaire::where('type', 'EDOM')
            ->where('status', 'ACTIVE')
            ->first();

        $academicYear = AcademicYear::where('status', 1)->first();

        return view('admin.edom.index', compact('questionnaires', 'activeQuestionnaire', 'academicYear'));
    }

    public function createQuestionnaire()
    {
        $categories = [
            'PERENCANAAN' => 'Perencanaan Pembelajaran',
            'PELAKSANAAN' => 'Pelaksanaan Pembelajaran',
            'EVALUASI' => 'Evaluasi Pembelajaran',
            'KEPRIBADIAN' => 'Kepribadian Dosen'
        ];

        return view('admin.edom.questionnaire.create', compact('categories'));
    }

    public function storeQuestionnaire(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'questions' => 'required|array',
            'questions.*.category' => 'required|string',
            'questions.*.question_text' => 'required|string',
            'questions.*.weight' => 'required|numeric|between:1,5'
        ]);

        try {
            DB::beginTransaction();

            // Nonaktifkan kuesioner yang aktif sebelumnya
            if ($request->status === 'ACTIVE') {
                Questionnaire::where('type', 'EDOM')
                    ->where('status', 'ACTIVE')
                    ->update(['status' => 'INACTIVE']);
            }

            // Buat kuesioner baru
            $questionnaire = Questionnaire::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'type' => 'EDOM',
                'status' => $request->status ?? 'DRAFT',
                'academic_year_id' => AcademicYear::where('status', 1)->first()->id
            ]);

            // Simpan pertanyaan
            foreach ($validated['questions'] as $question) {
                Question::create([
                    'questionnaire_id' => $questionnaire->id,
                    'category' => $question['category'],
                    'question_text' => $question['question_text'],
                    'type' => 'RATING',
                    'weight' => $question['weight']
                ]);
            }

            DB::commit();

            return redirect()->route('admin.edom.questionnaire.index')
                ->with('success', 'Kuesioner EDOM berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function editQuestionnaire(Questionnaire $questionnaire)
    {
        $categories = [
            'PERENCANAAN' => 'Perencanaan Pembelajaran',
            'PELAKSANAAN' => 'Pelaksanaan Pembelajaran',
            'EVALUASI' => 'Evaluasi Pembelajaran',
            'KEPRIBADIAN' => 'Kepribadian Dosen'
        ];

        $questions = $questionnaire->questions()->orderBy('category')->get();

        return view(
            'admin.edom.questionnaire.edit',
            compact('questionnaire', 'categories', 'questions')
        );
    }

    public function updateQuestionnaire(Request $request, Questionnaire $questionnaire)
    {
        // ... validation and update logic ...
    }

    public function toggleStatus(Questionnaire $questionnaire)
    {
        try {
            DB::beginTransaction();

            // Nonaktifkan kuesioner aktif lainnya jika akan mengaktifkan
            if ($questionnaire->status !== 'ACTIVE') {
                Questionnaire::where('type', 'EDOM')
                    ->where('status', 'ACTIVE')
                    ->update(['status' => 'INACTIVE']);

                $questionnaire->update(['status' => 'ACTIVE']);
                $message = 'Kuesioner berhasil diaktifkan';
            } else {
                $questionnaire->update(['status' => 'INACTIVE']);
                $message = 'Kuesioner berhasil dinonaktifkan';
            }

            DB::commit();
            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}