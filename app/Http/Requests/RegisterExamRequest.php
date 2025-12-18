<?php

namespace App\Http\Requests;

use App\Models\ThesisExam;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RegisterExamRequest extends FormRequest
{
    /**
     * Tentukan apakah user berhak melakukan request ini.
     */
    public function authorize(): bool
    {
        return $this->user()->can('registerExam', $this->route('thesis'));
    }

    /**
     * Aturan validasi yang berlaku untuk request.
     */
    public function rules(): array
    {
        $thesis = $this->route('thesis');
        $latestExam = $thesis->exams()->latest()->first();

        // Tentukan apakah ini pendaftaran ulang setelah ditolak
        $isReregistrationAfterRejected = $latestExam && $latestExam->status === 'ditolak';

        $documentRules = [
            'file',
            'mimes:pdf',
            'max:2048',
        ];

        return [
            // exam_type selalu required (walaupun hidden)
            'exam_type' => 'required|string|in:proposal,hasil,tutup',

            // Dokumen required hanya jika bukan pendaftaran ulang setelah ditolak
            'lembar_persetujuan' => [
                $isReregistrationAfterRejected ? 'nullable' : 'required',
                ...$documentRules
            ],
            'draft_skripsi' => [
                $isReregistrationAfterRejected ? 'nullable' : 'required',
                ...$documentRules
            ],
            'bukti_pembayaran' => [
                $isReregistrationAfterRejected ? 'nullable' : 'required',
                ...$documentRules
            ],
        ];
    }

    /**
     * Konfigurasi validator setelah validasi utama.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $thesis = $this->route('thesis');
            $latestExam = $thesis->exams()->latest()->first();

            // Logika baru untuk mencegah pengajuan saat status sedang diproses
            if ($latestExam && in_array($latestExam->status, ['diajukan', 'dijadwalkan', 'pelaksanaan', 'selesai'])) {
            }
        });
    }
}