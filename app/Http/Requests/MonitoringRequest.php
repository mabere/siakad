<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonitoringRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'schedule_id' => 'required|exists:schedules,id',
            'meeting_number' => 'required|integer|min:1|max:16',
            'monitoring_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'attendance_count' => 'required|integer|min:0',
            'material_conformity' => 'required|boolean',
            'learning_method' => 'required|string|max:255',
            'media_used' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'aspects' => 'nullable|array',
            'aspects.*.score' => 'required|integer|min:1|max:4',
            'aspects.*.notes' => 'nullable|string'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'schedule_id.required' => 'Jadwal kuliah harus dipilih',
            'meeting_number.required' => 'Pertemuan ke-berapa harus diisi',
            'meeting_number.min' => 'Pertemuan minimal ke-1',
            'meeting_number.max' => 'Pertemuan maksimal ke-16',
            'monitoring_date.required' => 'Tanggal monitoring harus diisi',
            'start_time.required' => 'Waktu mulai harus diisi',
            'end_time.required' => 'Waktu selesai harus diisi',
            'end_time.after' => 'Waktu selesai harus setelah waktu mulai',
            'attendance_count.required' => 'Jumlah kehadiran harus diisi',
            'attendance_count.min' => 'Jumlah kehadiran minimal 0',
            'material_conformity.required' => 'Kesesuaian materi harus dipilih',
            'learning_method.required' => 'Metode pembelajaran harus diisi',
            'media_used.required' => 'Media pembelajaran harus diisi',
            'aspects.*.score.required' => 'Skor aspek harus diisi',
            'aspects.*.score.min' => 'Skor minimal 1',
            'aspects.*.score.max' => 'Skor maksimal 4'
        ];
    }
}