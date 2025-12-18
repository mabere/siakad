<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlumniRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules()
    {
        return [
            'student_id' => 'required|exists:students,id|unique:alumni,student_id',
            'graduation_year' => 'required|integer|min:1900|max:' . (now()->year + 1),
            'job_title' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'salary_range' => 'nullable|string|max:100',
            'further_education' => 'nullable|string|max:255',
            'contribution' => 'nullable|string',
            'status' => 'required|in:aktif,non-aktif',
            'visibility' => 'required|in:public,internal,private',
        ];
    }
}