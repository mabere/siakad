<?php

namespace App\Http\Controllers\Backend;

use App\Models\Course;
use App\Models\Curriculum;
use App\Models\Department;
use App\Models\MkduCourse;
use Illuminate\Http\Request;
use App\Exports\CourseExport;
use App\Imports\CourseImport;
use App\Services\CourseService;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
        $this->middleware('auth');
    }


    // public function index(Curriculum $curriculum)
    // {
    //     $this->authorize('viewAny', Course::class);

    //     // Ambil semua course kurikulum ini
    //     $prodiCourses = $curriculum->courses()->with('department')->get()->map(function ($course) {
    //         $course->source = 'prodi';
    //         return $course;
    //     });

    //     // Ambil semua MKDU
    //     $mkduCourses = MkduCourse::all()->map(function ($course) {
    //         $course->source = 'mkdu';
    //         $course->kategori = 'Wajib'; // default kalau belum ada
    //         $course->curriculum_id = null; // biar konsisten
    //         $course->prerequisites = null;
    //         return $course;
    //     });

    //     // Gabungkan dan kelompokkan berdasarkan semester
    //     $coursesBySemester = $prodiCourses->concat($mkduCourses)->sortBy('semester_number')->groupBy('semester_number');

    //     return view('admin.courses.index', compact('curriculum', 'coursesBySemester'));
    // }

    public function index(Curriculum $curriculum)
    {
        $this->authorize('viewAny', Course::class);
        $programCourses = $curriculum->courses()->with('department')->get();
        // 2. Muat Mata Kuliah MKDU (relasi 'mkduCourses')
        $mkduCourses = $curriculum->mkduCourses()->withPivot('semester_number')->get();
        // 3. Gabungkan kedua koleksi mata kuliah dan tambahkan atribut 'is_mkdu' serta 'semester_number_for_grouping'
        $allCourses = collect();
        // Tambahkan mata kuliah prodi ke koleksi gabungan
        foreach ($programCourses as $course) {
            $course->is_mkdu = false;
            // Untuk mata kuliah prodi, semester_number langsung ada di objek course
            $course->semester_number_for_grouping = $course->semester_number;
            $allCourses->push($course);
        }

        // Tambahkan mata kuliah MKDU ke koleksi gabungan
        foreach ($mkduCourses as $mkduCourse) {
            $mkduCourse->is_mkdu = true; // Flag untuk identifikasi
            // Untuk MKDU, semester_number ada di pivot
            $mkduCourse->semester_number_for_grouping = $mkduCourse->pivot->semester_number;
            $allCourses->push($mkduCourse);
        }

        // 4. Urutkan dan Kelompokkan berdasarkan semester_number_for_grouping
        $coursesBySemester = $allCourses->sortBy('semester_number_for_grouping')
            ->groupBy('semester_number_for_grouping');

        return view('admin.courses.index', compact('curriculum', 'coursesBySemester'));
    }

    public function create(Curriculum $curriculum)
    {
        $this->authorize('create', Course::class);
        $availableCourses = Course::where('curriculum_id', $curriculum->id)->orWhereNull('curriculum_id')->where('department_id', $curriculum->department_id)->get();
        return view('admin.courses.create', compact('curriculum', 'availableCourses'));
    }

    public function store(Request $request, Curriculum $curriculum)
    {
        $this->authorize('create', Course::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code',
            'sks' => 'required|integer|min:1',
            'semester_number' => 'required|integer|min:1|max:8',
            'kategori' => 'required|in:Wajib,Pilihan',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'exists:courses,id',
            'syllabus_path' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $this->courseService->createCourse($curriculum, $validated);
        return redirect()->route('curriculums.courses.index', $curriculum)->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function edit(Curriculum $curriculum, Course $course)
    {
        $this->authorize('update', $course);
        $availableCourses = Course::where('curriculum_id', $curriculum->id)
            ->orWhereNull('curriculum_id')
            ->where('department_id', $curriculum->department_id)
            ->where('id', '!=', $course->id)
            ->get();
        return view('admin.courses.edit', compact('curriculum', 'course', 'availableCourses'));
    }

    public function update(Request $request, Curriculum $curriculum, Course $course)
    {
        $this->authorize('update', $course);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code,' . $course->id,
            'sks' => 'required|integer|min:1',
            'semester_number' => 'required|integer|min:1|max:8',
            'kategori' => 'required|in:Wajib,Pilihan',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'exists:courses,id',
            'syllabus_path' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $this->courseService->updateCourse($course, $validated);
        return redirect()->route('curriculums.courses.index', $curriculum)->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(Curriculum $curriculum, Course $course)
    {
        $this->authorize('delete', $course);
        $this->courseService->deleteCourse($course);
        return redirect()->route('curriculums.courses.index', $curriculum)->with('success', 'Mata kuliah berhasil dihapus.');
    }

    public function export(Curriculum $curriculum)
    {
        $this->authorize('export', Course::class);
        return Excel::download(new CourseExport($curriculum->id), 'courses.xlsx');
    }

    public function import(Request $request, Curriculum $curriculum)
    {
        // dd($request->all());
        $this->authorize('import', Course::class);
        $request->validate([
            'file' => 'required|mimes:xlsx|max:2048',
        ]);

        Excel::import(new CourseImport($curriculum->id), $request->file('file')->store('temp'));
        return redirect()->route('curriculums.courses.index', $curriculum)->with('success', 'Mata kuliah berhasil diimpor.');
    }

}