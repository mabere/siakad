<?php

namespace App\Imports;

use App\Models\Course;
use App\Models\Curriculum;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Log;

class CourseImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $curriculumId;

    public function __construct($curriculumId)
    {
        $this->curriculumId = $curriculumId;
    }

    public function model(array $row)
    {
        try {
            $prerequisites = [];
            if (!empty($row['prasyarat']) && is_string($row['prasyarat']) && trim($row['prasyarat']) !== '-' && trim($row['prasyarat']) !== '') {
                $prerequisiteCodes = array_filter(array_map('trim', explode(',', $row['prasyarat'])), function ($code) {
                    return !empty($code) && $code !== '\\';
                });
                if (!empty($prerequisiteCodes)) {
                    $prerequisites = Course::whereIn('code', $prerequisiteCodes)
                        ->where('curriculum_id', $this->curriculumId)
                        ->pluck('id')
                        ->toArray();
                }
            }

            $curriculum = Curriculum::findOrFail($this->curriculumId);

            $course = new Course([
                'curriculum_id' => $this->curriculumId,
                'department_id' => $curriculum->department_id,
                'name' => $row['nama_mata_kuliah'],
                'code' => $row['kode'],
                'sks' => $row['sks'],
                'semester_number' => $row['semester'],
                'kategori' => $row['kategori'],
                'prerequisites' => $prerequisites,
            ]);

            return $course;
        } catch (\Exception $e) {
            Log::error('Failed to import course', [
                'error' => $e->getMessage(),
                'row' => $row,
                'user_id' => auth()->id(),
            ]);
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            'nama_mata_kuliah' => 'required|string|max:255',
            'kode' => 'required|string|max:10|unique:courses,code',
            'sks' => 'required|integer|min:1',
            'semester' => 'required|integer|min:1|max:8',
            'kategori' => 'required|in:Wajib,Pilihan',
            'prasyarat' => 'nullable|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_mata_kuliah.required' => 'Nama mata kuliah wajib diisi.',
            'kode.required' => 'Kode mata kuliah wajib diisi.',
            'kode.unique' => 'Kode mata kuliah sudah digunakan.',
            'sks.required' => 'SKS wajib diisi.',
            'semester.required' => 'Semester wajib diisi.',
            'kategori.required' => 'Kategori wajib diisi.',
        ];
    }
}
