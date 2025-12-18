<?php

namespace App\Http\Requests;

use App\Models\Alumni;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAlumniRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && (auth()->user()->hasRole('admin') ||
            (auth()->user()->hasRole('mahasiswa') && Alumni::where('student_id', auth()->user()->student->id)->exists()));
    }

    public function rules()
    {
        return [
            'graduation_year' => 'required|integer|min:1900|max:' . (now()->year + 1),
            'job_title' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'salary_range' => 'nullable|string|max:100',
            'further_education' => 'nullable|string|max:255',
            'contribution' => 'nullable|string',
            'status' => 'nullable|in:aktif,non-aktif',
            'visibility' => 'required|in:public,internal,private',
        ];
    }
}
