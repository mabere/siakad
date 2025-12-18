<?php

namespace App\Http\Controllers\Backend;

use App\Models\Curriculum;
use App\Models\MkduCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class MkduCourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkRole:admin');
    }

    public function index()
    {
        $mkduCourses = MkduCourse::with([
            'curricula' => function ($query) {
                $query->withPivot('semester_number');
            }
        ])->get();
        return view('admin.mkdu.index', compact('mkduCourses'));
    }

    public function create()
    {
        $curricula = Curriculum::all();
        return view('admin.mkdu.create', compact('curricula'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:mkdu_courses,code',
            'name' => 'required|string|max:255',
            'sks' => 'required|integer|min:1',
            'semester_number' => 'required|integer|min:1',
            'syllabus_path' => 'nullable|file|mimes:pdf|max:2048',
            'curriculum_id' => 'required|exists:curricula,id',
        ]);

        try {
            $syllabusPath = null;
            if ($request->hasFile('syllabus_path')) {
                $syllabusPath = $request->file('syllabus_path')->store('syllabuses', 'public');
            }

            $mkduCourse = MkduCourse::create([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'sks' => $validated['sks'],
                'semester_number' => $validated['semester_number'],
                'syllabus_path' => $syllabusPath,
            ]);

            $mkduCourse->curricula()->attach($validated['curriculum_id'], [
                'semester_number' => $validated['semester_number']
            ]);

            Log::info('MKDU course created and attached to curriculum', [
                'mkdu_course_id' => $mkduCourse->id,
                'curriculum_id' => $validated['curriculum_id'],
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('mkdu.index')->with('success', 'Mata kuliah MKDU berhasil dibuat dan dihubungkan.');
        } catch (\Exception $e) {
            Log::error('Failed to create MKDU course or attach to curriculum', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);
            return redirect()->route('mkdu.index')->withErrors(['error' => 'Gagal membuat mata kuliah MKDU: ' . $e->getMessage()]);
        }
    }

    public function edit(MkduCourse $mkdu)
    {
        $curricula = Curriculum::all();
        return view('admin.mkdu.edit', compact('mkdu', 'curricula'));
    }

    public function update(Request $request, MkduCourse $mkdu)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:mkdu_courses,code,' . $mkdu->id,
            'name' => 'required|string|max:255',
            'sks' => 'required|integer|min:1',
            'semester_number' => 'required|integer|min:1',
            'syllabus_path' => 'nullable|file|mimes:pdf|max:2048',
            'curriculum_id' => 'required|exists:curricula,id',
        ]);

        try {
            $syllabusPath = $mkdu->syllabus_path;
            if ($request->hasFile('syllabus_path')) {
                // Hapus syllabus lama jika ada
                if ($syllabusPath && Storage::disk('public')->exists($syllabusPath)) {
                    Storage::disk('public')->delete($syllabusPath);
                }
                $syllabusPath = $request->file('syllabus_path')->store('syllabuses', 'public');
            }

            $mkdu->update([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'sks' => $validated['sks'],
                'syllabus_path' => $syllabusPath,
            ]);

            // Detach existing curricula
            $mkdu->curricula()->sync([
                $validated['curriculum_id'] => ['semester_number' => $validated['semester_number']]
            ]);


            Log::info('MKDU course updated and curriculum relation synced', [
                'mkdu_course_id' => $mkdu->id,
                'curriculum_id' => $validated['curriculum_id'],
                'semester_number_pivot' => $validated['semester_number'],
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('mkdu.index')->with('success', 'Mata kuliah MKDU berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Failed to update MKDU course or sync curriculum relation', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);
            return redirect()->route('mkdu.index')->withErrors(['error' => 'Gagal memperbarui mata kuliah MKDU: ' . $e->getMessage()]);
        }
    }

    public function destroy(MkduCourse $mkdu)
    {
        if (!$mkdu->exists) {
            Log::warning('MKDU course not found for deletion', [
                'mkdu_course_id' => request()->route('mkdu'),
                'user_id' => auth()->id(),
            ]);
            return redirect()->route('mkdu.index')->withErrors(['error' => 'Mata kuliah MKDU tidak ditemukan.']);
        }

        try {
            // Detach dari pivot curriculum_mkdu_course
            $mkdu->curricula()->detach();

            // Hapus file silabus jika ada
            if ($mkdu->syllabus_path) {
                Storage::disk('public')->delete($mkdu->syllabus_path);
            }

            // Hapus record MKDU
            $mkduCourseId = $mkdu->id;
            $mkdu->delete();

            Log::info('MKDU course deleted', [
                'mkdu_course_id' => $mkduCourseId,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('mkdu.index')->with('success', 'Mata kuliah MKDU berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Failed to delete MKDU course', [
                'error' => $e->getMessage(),
                'mkdu_course_id' => $mkdu->id,
                'user_id' => auth()->id(),
            ]);
            return redirect()->route('mkdu.index')->withErrors(['error' => 'Gagal menghapus mata kuliah MKDU: ' . $e->getMessage()]);
        }
    }
}