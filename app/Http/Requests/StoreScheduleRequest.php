<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('scheduleExam', $this->thesis);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'scheduled_at' => 'required|date|after:now',
            'location'     => 'required|string|max:255',
            'chairman_id'  => 'required|exists:lecturers,id',
            // Sekretaris tidak boleh orang yang sama dengan ketua
            'secretary_id' => 'required|exists:lecturers,id|different:chairman_id',
        ];
    }

    public function messages(): array
    {
        return [
            'secretary_id.different' => 'Sekretaris tidak boleh orang yang sama dengan Ketua Sidang.',
            'scheduled_at.after' => 'Tanggal ujian harus setelah hari ini.',
        ];
    }
}
