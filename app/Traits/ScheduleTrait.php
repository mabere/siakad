<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait ScheduleTrait
{
    protected function validateScheduleData(array $data)
    {
        $rules = [
            'department_id' => 'required|integer|exists:departments,id',
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'course_id' => 'nullable|integer|exists:courses,id',
            'mkdu_course_id' => 'nullable|integer|exists:mkdu_courses,id',
            'room_id' => 'required|exists:rooms,id',
            'kelas_id' => 'required|integer|exists:kelas,id',
            'lecturer1_id' => 'nullable|exists:lecturers,id',
            'lecturer1_start' => 'nullable|integer|min:1',
            'lecturer1_end' => 'nullable|integer|min:1|gte:lecturer1_start',
            'lecturer2_id' => 'nullable|exists:lecturers,id',
            'lecturer2_start' => 'nullable|integer|min:1',
            'lecturer2_end' => 'nullable|integer|min:1|gte:lecturer2_start',
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'start_time' => 'required',
            'end_time' => 'required|after_or_equal:start_time',
            'is_mkdu' => 'required|boolean',
        ];

        Validator::extend('required_one', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            if ((bool) $data['is_mkdu']) {
                return !empty($data['mkdu_course_id']);
            }
            return !empty($data['course_id']);
        }, 'Salah satu dari mata kuliah atau mata kuliah umum wajib diisi.');

        $rules['course_id'] = ['nullable', 'integer', 'exists:courses,id', 'required_one'];
        $rules['mkdu_course_id'] = ['nullable', 'integer', 'exists:mkdu_courses,id', 'required_one'];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            Log::error('ScheduleTrait::validateScheduleData - Validation failed', $validator->errors()->toArray());
            throw new ValidationException($validator);
        }

        return $validator->validate();
    }

}