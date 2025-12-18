<?php

namespace App\Http\Controllers\Edom;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Questionnaire;
use App\Models\Question;
use App\Models\Response;
use App\Models\AcademicYear;
use App\Models\EdomCategory;
use Illuminate\Support\Str;

class EdomManagementController extends Controller
{
    public function index()
    {
        $questionnaires = Questionnaire::where('type', 'EDOM')
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->get();

        $activeQuestionnaire = Questionnaire::where('type', 'EDOM')
            ->where('status', 'ACTIVE')
            ->withCount('questions')
            ->first();

        $academicYear = AcademicYear::where('status', 1)->first();

        return view('admin.edom.index', compact('questionnaires', 'activeQuestionnaire', 'academicYear'));
    }

    public function create()
    {
        $categories = EdomCategory::pluck('value', 'id');

        return view('admin.edom.questionnaire.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|string|in:ACTIVE,DRAFT',
            'questions' => 'required|array|min:1',
            'questions.*.category' => 'required|exists:edom_categories,id', // Ubah ke 'id', bukan 'string'
            'questions.*.question_text' => 'required|string|max:1000',
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
                'status' => $validated['status'],
                'academic_year_id' => AcademicYear::where('status', 1)->firstOrFail()->id // Gunakan firstOrFail untuk menangkap error
            ]);
            // Simpan pertanyaan
            foreach ($validated['questions'] as $question) {
                Question::create([
                    'questionnaire_id' => $questionnaire->id,
                    'category' => $question['category'], // Ini sekarang adalah 'id' dari categoryName
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
            Log::error('Error creating questionnaire: ' . $e->getMessage(), ['exception' => $e->getTraceAsString()]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Questionnaire $questionnaire)
    {
        $categories = EdomCategory::orderBy('value')->get();
        $questions = $questionnaire->questions()
            ->whereNull('deleted_at')
            ->with('categoryName')
            ->get();

        return view('admin.edom.questionnaire.edit', compact(
            'questionnaire',
            'questions',
            'categories'
        ));
    }

    public function update(Request $request, Questionnaire $questionnaire)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'sometimes|string|in:ACTIVE,DRAFT',
                'questions' => 'required|array',
                'questions.*.id' => 'sometimes|integer|exists:questions,id', // Include ID for updates
                'questions.*.category' => 'required|exists:edom_categories,id',
                'questions.*.question_text' => 'required|string',
                'questions.*.weight' => 'required|numeric|between:1,5'
            ]);

            DB::beginTransaction();

            // Update questionnaire
            $questionnaire->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'status' => $request->status === 'ACTIVE' ? 'ACTIVE' : 'DRAFT'
            ]);

            $updatedQuestionIds = [];
            foreach ($validated['questions'] as $questionData) {
                if (isset($questionData['id'])) {
                    // Update existing question
                    $question = Question::findOrFail($questionData['id']);
                    $question->update([
                        'category' => $questionData['category'], // Ini adalah 'id' dari categoryName
                        'question_text' => $questionData['question_text'],
                        'weight' => $questionData['weight']
                    ]);
                    $updatedQuestionIds[] = $question->id;
                } else {
                    // Create new question
                    $newQuestion = Question::create([
                        'questionnaire_id' => $questionnaire->id,
                        'category' => $questionData['category'], // Ini adalah 'id' dari categoryName
                        'question_text' => $questionData['question_text'],
                        'type' => 'RATING',
                        'weight' => $questionData['weight']
                    ]);
                    $updatedQuestionIds[] = $newQuestion->id;
                }
            }

            // Soft delete questions that weren't updated
            Question::where('questionnaire_id', $questionnaire->id)
                ->whereNotIn('id', $updatedQuestionIds)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => now()]);

            // Handle active status if needed
            if ($request->status === 'ACTIVE') {
                Questionnaire::where('id', '!=', $questionnaire->id)
                    ->where('type', 'EDOM')
                    ->where('status', 'ACTIVE')
                    ->update(['status' => 'INACTIVE']);
            }

            DB::commit();
            return $this->respond($request, 'Kuesioner berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->handleErrorResponse($request, $e);
        }
    }

    public function destroy(Questionnaire $questionnaire)
    {
        try {
            // Cek apakah kuesioner memiliki respons aktif
            if (Response::where('questionnaire_id', $questionnaire->id)->exists()) {
                return redirect()->back()
                    ->with('error', 'Kuesioner ini tidak dapat dihapus karena masih memiliki respons aktif.')
                    ->withInput();
            }

            // Soft delete kuesioner dan pertanyaan terkait
            $questionnaire->delete(); // Ini akan soft delete kuesioner
            $questionnaire->questions()->update(['deleted_at' => now()]); // Soft delete semua pertanyaan terkait

            return redirect()->route('admin.edom.questionnaire.index')
                ->with('success', 'Kuesioner EDOM berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Error destroying questionnaire', [
                'id' => $questionnaire->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus kuesioner: ' . $e->getMessage())
                ->withInput();
        }
    }

    protected function respond(Request $request, $message)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('admin.edom.questionnaire.index')
            ]);
        }
        return redirect()->route('admin.edom.questionnaire.index')->with('success', $message);
    }

    protected function handleErrorResponse(Request $request, \Exception $e)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 422);
        }
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
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

    public function questions(Questionnaire $questionnaire)
    {
        $questions = $questionnaire->questions()->with('categoryName')->get();
        $categories = EdomCategory::pluck('value', 'id');
        return view('admin.edom.questions.index', compact('questionnaire', 'questions', 'categories'));
    }

}
