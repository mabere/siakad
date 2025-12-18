<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitRevisionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('reviseExam', $this->thesis);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // Nullable karena mahasiswa mungkin hanya merevisi salah satu dokumen
            'lembar_persetujuan' => 'nullable|file|mimes:pdf|max:2048',
            'draft_skripsi'      => 'nullable|file|mimes:pdf|max:2048',
            'bukti_pembayaran'      => 'nullable|file|mimes:pdf|max:2048',
            'lembar_persetujuan' => 'required_without:draft_skripsi',
        ];
    }

    public function messages(): array
    {
        return [
            'lembar_persetujuan.required_without' => 'Anda harus mengunggah setidaknya satu dokumen revisi.',
        ];
    }
}
