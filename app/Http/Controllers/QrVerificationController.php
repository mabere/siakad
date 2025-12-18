<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LetterRequest;
use Illuminate\Support\Facades\Log;

class QrVerificationController extends Controller
{
    public function showVerificationForm()
    {
        return view('verification-form');
    }

    public function verifyQrCode(Request $request)
    {
        $qrData = $request->input('qr_data');

        // Parse data QR code berdasarkan baris baru
        $lines = explode(PHP_EOL, $qrData);
        $data = [];
        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                [$key, $value] = explode(':', $line, 2);
                $data[trim($key)] = trim($value);
            }
        }

        // Pastikan data yang diperlukan ada
        if (!isset($data['Id'])) {
            return response()->json(['status' => 'error', 'message' => 'Data QR code tidak valid'], 400);
        }

        $letterId = $data['Id'];
        $letterRequest = LetterRequest::with(['approvedBy', 'approvedBy.lecturer'])->find($letterId);

        if (!$letterRequest) {
            return response()->json(['status' => 'error', 'message' => 'Dokumen tidak ditemukan'], 404);
        }

        $isValid = $this->validateQrData($data, $letterRequest);

        if ($isValid) {
            // Sesuaikan data yang dikembalikan berdasarkan jenis surat
            $letterType = $letterRequest->letterType->name ?? 'unknown';
            $verifiedData = $this->formatVerifiedData($data, $letterType);

            return response()->json([
                'status' => 'success',
                'message' => 'Dokumen Valid',
                'data' => $verifiedData
            ], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Dokumen Tidak Valid'], 400);
        }
    }

    private function validateQrData(array $qrData, $letterRequest)
    {
        // Ambil data dari database
        $approvedBy = $letterRequest->approvedBy;
        $lecturer = $approvedBy ? $approvedBy->lecturer : null;
        $formData = $letterRequest->form_data ?? [];

        $normalize = function ($string) {
            return trim(str_replace([' ', '.', ','], '', strtolower($string ?? '')));
        };

        $dekan = $approvedBy ? $approvedBy->name : 'Nama Dekan Tidak Tersedia';
        $nip = $lecturer ? $lecturer->nidn : ($approvedBy ? ($approvedBy->nip ?? 'NIP Tidak Tersedia') : 'NIP Tidak Tersedia');

        // Tentukan jenis surat (misalnya dari database atau QR code)
        $letterType = $letterRequest->letterType->name ?? 'unknown';

        // Validasi dasar yang sama untuk semua surat
        $validations = [
            'Dekan' => $normalize($dekan) === $normalize($qrData['Dekan']),
            'NIP' => trim($nip) === trim($qrData['NIP']),
            'Date' => trim('Unaaha, ' . $letterRequest->created_at->format('d F Y')) === trim($qrData['Date']),
            'No' => trim($letterRequest->reference_number ?? '') === trim($qrData['No']),
            'Nama' => trim($formData['Nama'] ?? '') === trim($qrData['Nama']),
            'NIM' => trim($formData['Nim'] ?? '') === trim($qrData['NIM']),
            'Prodi' => trim($formData['Prodi'] ?? '') === trim($qrData['Prodi']),
            'Id' => $letterRequest->id === (int) $qrData['Id'],
        ];

        // Validasi tambahan berdasarkan jenis surat
        if ($letterType === 'surat_aktif_kuliah') {
            $validations['Peruntukan'] = trim($formData['Peruntukan'] ?? '') === trim($qrData['Peruntukan']);
        } elseif ($letterType === 'surat_izin_penelitian') {
            $validations['Judul_Penelitian'] = trim($formData['Judul_Penelitian'] ?? '') === trim($qrData['Judul_Penelitian']);
            $validations['Lokasi_Penelitian'] = trim($formData['Lokasi_Penelitian'] ?? '') === trim($qrData['Lokasi_Penelitian']);
            $validations['Durasi_Penelitian'] = trim($formData['Durasi_Penelitian'] ?? '') === trim($qrData['Durasi_Penelitian']);
        } elseif ($letterType === 'surat_cuti_mahasiswa') {
            $validations['Alasan_Cuti'] = trim($formData['Alasan_Cuti'] ?? '') === trim($qrData['Alasan_Cuti']);
            $validations['Periode_Cuti'] = trim($formData['Periode_Cuti'] ?? '') === trim($qrData['Periode_Cuti']);
        }

        Log::info('Validation Results', $validations);

        return !in_array(false, $validations, true);
    }

    private function formatVerifiedData(array $data, $letterType)
    {
        // Data dasar yang sama untuk semua jenis surat
        $verifiedData = [
            'dekan' => $data['Dekan'],
            'nip' => $data['NIP'],
            'date' => $data['Date'],
            'reference_number' => $data['No'],
            'student_name' => $data['Nama'],
            'nim' => $data['NIM'],
            'program_study' => $data['Prodi'],
            'id' => $data['Id'],
        ];

        // Tambahkan data spesifik berdasarkan jenis surat
        if ($letterType === 'surat_aktif_kuliah') {
            $verifiedData['purpose'] = $data['Peruntukan'];
        } elseif ($letterType === 'surat_izin_penelitian') {
            $verifiedData['judul_penelitian'] = $data['Judul_Penelitian'];
            $verifiedData['lokasi_penelitian'] = $data['Lokasi_Penelitian'];
            $verifiedData['durasi_penelitian'] = $data['Durasi_Penelitian'];
        } elseif ($letterType === 'surat_cuti_mahasiswa') {
            $verifiedData['alasan_cuti'] = $data['Alasan_Cuti'];
            $verifiedData['periode_cuti'] = $data['Periode_Cuti'];
        }

        return $verifiedData;
    }

}