<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Curriculum;
use App\Models\MkduCourse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CourseService
{
    public function createCourse(Curriculum $curriculum, array $data)
    {
        try {
            $syllabusPath = null;
            if (isset($data['syllabus_path'])) {
                $syllabusPath = $data['syllabus_path']->store('syllabuses', 'public');
            }

            $course = Course::create([
                'curriculum_id' => $curriculum->id,
                'department_id' => $curriculum->department_id,
                'name' => $data['name'],
                'code' => $data['code'],
                'sks' => $data['sks'],
                'semester_number' => $data['semester_number'],
                'kategori' => $data['kategori'],
                'prerequisites' => $data['prerequisites'] ?? [],
                'syllabus_path' => $syllabusPath,
            ]);

            Log::info('Course created', [
                'course_id' => $course->id,
                'curriculum_id' => $curriculum->id,
                'user_id' => auth()->id(),
            ]);

            return $course;
        } catch (\Exception $e) {
            Log::error('Failed to create course', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            throw $e;
        }
    }

    public function updateCourse(Course $course, array $data)
    {
        try {
            $syllabusPath = $course->syllabus_path;
            if (isset($data['syllabus_path'])) {
                if ($syllabusPath) {
                    Storage::disk('public')->delete($syllabusPath);
                }
                $syllabusPath = $data['syllabus_path']->store('syllabuses', 'public');
            }

            $course->update([
                'name' => $data['name'],
                'code' => $data['code'],
                'sks' => $data['sks'],
                'semester_number' => $data['semester_number'],
                'kategori' => $data['kategori'],
                'prerequisites' => $data['prerequisites'] ?? [],
                'syllabus_path' => $syllabusPath,
            ]);

            Log::info('Course updated', [
                'course_id' => $course->id,
                'curriculum_id' => $course->curriculum_id,
                'user_id' => auth()->id(),
            ]);

            return $course;
        } catch (\Exception $e) {
            Log::error('Failed to update course', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            throw $e;
        }
    }

    public function deleteCourse(Course $course)
    {
        try {
            if ($course->syllabus_path) {
                Storage::disk('public')->delete($course->syllabus_path);
            }
            $course->delete();

            Log::info('Course deleted', [
                'course_id' => $course->id,
                'curriculum_id' => $course->curriculum_id,
                'user_id' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete course', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            throw $e;
        }
    }

    public function copyCourses(Curriculum $originalCurriculum, Curriculum $newCurriculum)
    {
        try {
            // Salin mata kuliah prodi-spesifik
            $courses = $originalCurriculum->courses()->whereNotIn('code', MkduCourse::pluck('code'))->get();
            $oldToNewCourseIds = [];

            foreach ($courses as $course) {
                $newSyllabusPath = null;
                if ($course->syllabus_path) {
                    $newSyllabusPath = 'syllabuses/' . uniqid() . '_' . basename($course->syllabus_path);
                    Storage::disk('public')->copy($course->syllabus_path, $newSyllabusPath);
                }

                $newCourse = Course::create([
                    'curriculum_id' => $newCurriculum->id,
                    'department_id' => $newCurriculum->department_id,
                    'name' => $course->name,
                    'code' => $this->generateUniqueCode($course->code, $newCurriculum->id),
                    'sks' => $course->sks,
                    'semester_number' => $course->semester_number,
                    'kategori' => $course->kategori,
                    'prerequisites' => [],
                    'syllabus_path' => $newSyllabusPath,
                ]);

                Log::info('Course copied', [
                    'original_course_id' => $course->id,
                    'new_course_id' => $newCourse->id,
                    'new_curriculum_id' => $newCurriculum->id,
                    'user_id' => auth()->id(),
                ]);

                $oldToNewCourseIds[$course->id] = $newCourse->id;
            }

            // Update prasyarat
            foreach ($courses as $course) {
                if (!empty($course->prerequisites)) {
                    $newPrerequisites = array_map(function ($oldId) use ($oldToNewCourseIds) {
                        return $oldToNewCourseIds[$oldId] ?? null;
                    }, $course->prerequisites);
                    $newPrerequisites = array_filter($newPrerequisites);

                    Course::where('id', $oldToNewCourseIds[$course->id])
                        ->update(['prerequisites' => $newPrerequisites]);
                }
            }

            // Hubungkan MKDU ke kurikulum baru
            $mkduCourses = $originalCurriculum->mkduCourses()->get();
            $newCurriculum->mkduCourses()->sync($mkduCourses->pluck('id'));

            Log::info('MKDU courses attached', [
                'new_curriculum_id' => $newCurriculum->id,
                'mkdu_course_ids' => $mkduCourses->pluck('id')->toArray(),
                'user_id' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to copy courses', [
                'error' => $e->getMessage(),
                'original_curriculum_id' => $originalCurriculum->id,
                'new_curriculum_id' => $newCurriculum->id,
                'user_id' => auth()->id(),
            ]);
            throw $e;
        }
    }

    protected function generateUniqueCode($originalCode, $curriculumId)
    {
        $code = $originalCode;
        $suffix = 1;
        while (Course::where('code', $code)->where('curriculum_id', $curriculumId)->exists()) {
            $code = $originalCode . '-' . $suffix++;
        }
        return $code;
    }

}
