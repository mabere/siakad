<?php

namespace App\Http\Controllers\Backend;

use App\Models\Curriculum;
use Illuminate\Http\Request;
use App\Services\CourseService;
use App\Services\CurriculumService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CurriculumController extends Controller
{
    protected $curriculumService, $courseService;

    public function __construct(CourseService $courseService, CurriculumService $curriculumService)
    {
        $this->courseService = $courseService;
        $this->curriculumService = $curriculumService;
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('viewAny', Curriculum::class);
        $curriculums = Curriculum::with(['department', 'academicYear'])->get();
        return view('backend.kurikulum.index', compact('curriculums'));
    }

    public function create()
    {
        $this->authorize('create', Curriculum::class);
        return view('backend.kurikulum.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Curriculum::class);
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,active,archived',
        ]);

        $this->curriculumService->createCurriculum($validated);
        return redirect()->route('curriculums.index')->with('success', 'Kurikulum berhasil dibuat.');
    }

    // public function show(Curriculum $curriculum)
    // {
    //     $this->authorize('view', $curriculum);
    //     $courses = $curriculum->courses()->with('department')->get();
    //     $mkduCourses = $curriculum->mkduCourses()->withPivot('semester_number')->get();
    //     $allCourses = collect();
    //     foreach ($courses as $course) {
    //         $course->type = 'Program Studi';
    //         $course->semester_number_for_grouping = $course->semester_number;
    //         $allCourses->push($course);
    //     }

    //     // Tambahkan mata kuliah MKDU ke koleksi gabungan
    //     foreach ($mkduCourses as $mkduCourse) {
    //         $mkduCourse->type = 'MKDU';
    //         $mkduCourse->semester_number_for_grouping = $mkduCourse->pivot->semester_number;
    //         $allCourses->push($mkduCourse);
    //     }
    //     $groupedCourses = $allCourses->sortBy('semester_number_for_grouping')
    //                                 ->groupBy('semester_number_for_grouping');

    //     return view('backend.kurikulum.show', compact('curriculum', 'groupedCourses'));
    // }


    public function show(Curriculum $curriculum)
    {
        $this->authorize('view', $curriculum);
        return view('backend.kurikulum.show', compact('curriculum'));
    }

    public function edit(Curriculum $curriculum)
    {
        $this->authorize('update', $curriculum);
        return view('backend.kurikulum.edit', compact('curriculum'));
    }

    public function update(Request $request, Curriculum $curriculum)
    {
        $this->authorize('update', $curriculum);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,active,archived',
        ]);

        $this->curriculumService->updateCurriculum($curriculum, $validated);
        return redirect()->route('curriculums.index')->with('success', 'Kurikulum berhasil diperbarui.');
    }

    public function destroy(Curriculum $curriculum)
    {
        $this->authorize('delete', $curriculum);
        $this->curriculumService->deleteCurriculum($curriculum);
        return redirect()->route('curriculums.index')->with('success', 'Kurikulum berhasil dihapus.');
    }

    public function copy(Request $request, Curriculum $curriculum)
    {
        $this->authorize('create', Curriculum::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'status' => 'required|in:active,draft,archived',
        ]);

        try {
            // Buat kurikulum baru
            $newCurriculum = Curriculum::create([
                'name' => $validated['name'],
                'academic_year_id' => $validated['academic_year_id'],
                'department_id' => $curriculum->department_id,
                'status' => $validated['status'],
            ]);

            // Salin mata kuliah
            $this->courseService->copyCourses($curriculum, $newCurriculum);

            Log::info('Curriculum copied', [
                'original_curriculum_id' => $curriculum->id,
                'new_curriculum_id' => $newCurriculum->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('curriculums.index')->with('success', 'Kurikulum berhasil disalin.');
        } catch (\Exception $e) {
            Log::error('Failed to copy curriculum', [
                'error' => $e->getMessage(),
                'curriculum_id' => $curriculum->id,
                'user_id' => auth()->id(),
            ]);
            return redirect()->route('curriculums.index')->withErrors(['error' => 'Gagal menyalin kurikulum: ' . $e->getMessage()]);
        }
    }
}