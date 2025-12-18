<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignExaminersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('assignExaminers', $this->thesis);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Ambil ID dosen pembimbing dari relasi
        $supervisors = $this->thesis->supervisions->pluck('supervisor_id')->toArray();

        return [
            'examiners' => [
                'required',
                'array',
                'size:3',
                // Validasi custom menggunakan closure
                function ($attribute, $value, $fail) use ($supervisors) {
                    // Cek #1: Pastikan 3 penguji yang dipilih unik (tidak ada duplikat)
                    if (count(array_unique($value)) < 3) {
                        $fail('Tiga dosen penguji yang dipilih harus berbeda.');
                    }
                    // Cek #2: Pastikan penguji bukan salah satu dari pembimbing
                    if (array_intersect($supervisors, $value)) {
                        $fail('Dosen pembimbing tidak boleh ditetapkan sebagai penguji.');
                    }
                }
            ],
            // Pastikan setiap ID penguji ada di tabel lecturers
            'examiners.*' => 'required|exists:lecturers,id',
        ];
    }
}
