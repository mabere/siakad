<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Validasi policy sudah ditangani di controller
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required'],
            'angkatan' => ['required'],
            'total' => ['required', 'numeric'],
        ];

        if (auth()->user()->hasRole('admin')) {
            $rules['department_id'] = ['required', 'exists:departments,id'];
            $rules['advisor_id'] = ['required', 'exists:lecturers,id'];
        } else if (auth()->user()->hasRole('staff')) {
            $rules['lecturer_id'] = ['required', 'exists:lecturers,id'];
        }

        return $rules;
    }

    public function validatedForStore(): array
    {
        $data = $this->validated();

        if (auth()->user()->hasRole('staff')) {
            $data['department_id'] = auth()->user()->employee->department_id;
            $data['lecturer_id'] = $data['lecturer_id'];
        }

        if (auth()->user()->hasRole('admin')) {
            $data['lecturer_id'] = $data['advisor_id'];
        }

        return $data;
    }

    public function validatedForUpdate(): array
    {
        // Sama seperti store, tapi department_id tidak perlu diubah
        return $this->validatedForStore();
    }
}
