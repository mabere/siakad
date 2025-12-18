<?php

namespace App\Traits;

use BaconQrCode\Writer;
use Barryvdh\DomPDF\PDF;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\LetterRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use BaconQrCode\Renderer\ImageRenderer;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

trait ProcessesLetterRequest
{
    public function process(Request $request, LetterRequest $letterRequest)
    {
        try {
            $user = Auth::user();
            if ($user->hasRole('dekan')) {
                if ($letterRequest->user->student->department->faculty_id !== $user->lecturer->department->faculty_id) {
                    return redirect()->back()->with('error', 'Anda hanya dapat memproses surat dari fakultas Anda.');
                }
            }
            $letterRequest->update([
                'status' => 'approved',
                'notes' => $request->input('notes'),
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
            if (empty($letterRequest->reference_number)) {
                $letterRequest->reference_number = $this->generateReferenceNumber($letterRequest);
                $letterRequest->save();
            }
            if (empty($letterRequest->document_path)) {
                $faculty = $letterRequest->user->student->department->faculty;
                $student = $letterRequest->user->student;
                $additionalData = $letterRequest->form_data ?? [];
                $pdf = $this->generatePdfByLetterType($letterRequest, $faculty, $student, $additionalData);
                $pdfPath = 'public/letters/' . $letterRequest->id . '.pdf';
                Storage::put($pdfPath, $pdf->output());
                $letterRequest->document_path = str_replace('public/', '', $pdfPath);
                $letterRequest->save();
            }
            return redirect()->route('dekan.dashboard')->with('success', 'Surat berhasil disetujui.');
        } catch (\Exception $e) {
            Log::error('Error processing letter request:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses surat');
        }
    }

    public function generatePdfByLetterType(LetterRequest $letterRequest, $faculty, $student, array $additionalData = [])
    {
        $letterType = $letterRequest->letterType;
        if (!$letterType->active || empty($letterType->template)) {
            Log::error('Template surat tidak tersedia', ['letter_id' => $letterRequest->id]);
            abort(404, 'Template surat tidak tersedia.');
        }
        $templatePath = 'admin.letters.template.' . str_replace('_', '-', $letterType->template);
        $logoUri = base64_encode(file_get_contents(public_path('images/logo_unilaki.png')));
        $logoUri2 = base64_encode(file_get_contents(public_path('images/tutwuri.png')));
        // Data untuk QR code dari signature
        $approvedBy = $letterRequest->approvedBy;
        $lecturer = $approvedBy ? $approvedBy->lecturer : null;
        // Tambahan data
        $date = 'Unaaha, ' . now()->format('d F Y');
        $signerRole = 'Dekan';
        $deanName = $approvedBy ? $approvedBy->name : 'Nama Dekan Tidak Tersedia';
        $deanNip = $lecturer ? $lecturer->nidn : ($approvedBy ? ($approvedBy->nip ?? 'NIP Tidak Tersedia') : 'NIP Tidak Tersedia');
        $referenceNumber = $letterRequest->reference_number ?? 'No Reference';
        //Required_fileds
        $formData = $letterRequest->form_data ?? [];
        $studentName = $formData['Nama'] ?? 'Nama Tidak Tersedia';
        $studentNim = $formData['Nim'] ?? 'NIM Tidak Tersedia';
        $programStudy = $formData['Prodi'] ?? 'Program Studi Tidak Tersedia';
        $purpose = $formData['Peruntukan'] ?? 'Tujuan Tidak Tersedia';
        $letterId = $letterRequest->id;
        //Data QR-Code
        $barcodeData = "Dekan: " . str_replace(' ', '', $deanName) . PHP_EOL .
            "NIP: {$deanNip}" . PHP_EOL .
            "Date: {$date}" . PHP_EOL .
            "No: {$referenceNumber}" . PHP_EOL .
            "Nama: {$studentName}" . PHP_EOL .
            "NIM: {$studentNim}" . PHP_EOL .
            "Prodi: {$programStudy}" . PHP_EOL .
            "Peruntukan: {$purpose}" . PHP_EOL .
            "Id: {$letterId}";
        // Gunakan BaconQrCode dengan backend SVG (tanpa imagick)
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($barcodeData);
        // Encode SVG ke base64 untuk disematkan di PDF
        $barcodeImage = base64_encode($qrCodeSvg);

        $activeAcademicYear = AcademicYear::where('status', 1)->first();
        if (!$activeAcademicYear) {
            Log::warning('Tidak ada tahun akademik aktif ditemukan');
            $academicYear = 'Tidak Ada Tahun Akademik Aktif';
        } else {
            $academicYear = $activeAcademicYear->ta;
        }
        // Data
        $data = [
            'user' => $letterRequest->user->name,
            'type' => $letterRequest->letterType->name,
            'reference_number' => $letterRequest->reference_number,
            'date' => now()->format('d F Y'),
            'form_data' => $letterRequest->form_data,
            'Peruntukan' => $letterRequest->form_data['Peruntukan'] ?? 'Default Value',
            'academic_year' => $academicYear,
            'letterRequest' => $letterRequest,
            'dean_name' => $deanName,
            'dean_nip' => $deanNip,
            'logoUri' => $logoUri,
            'logoUri2' => $logoUri2,
            'barcodeImage' => $barcodeImage,
        ];
        $data = array_merge($data, $additionalData);
        $pdf = app(PDF::class)->loadView($templatePath, $data);
        return $pdf;
    }

    private function generateReferenceNumber(LetterRequest $letterRequest)
    {
        $prefix = $letterRequest->letterType->code ?? 'LTR';
        $year = date('Y');
        $month = date('m');
        // Konversi bulan ke angka romawi
        $romanMonths = ['01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV', '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII', '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'];
        $romanMonth = $romanMonths[$month];
        // Cari nomor referensi terakhir dengan pola yang sama
        $lastNumber = LetterRequest::where('reference_number', 'like', "%/{$prefix}/{$romanMonth}/{$year}")
            ->orderBy('reference_number', 'desc')
            ->first();
        // Set nomor urut awal
        $number = 1;
        // Jika ada nomor referensi sebelumnya, increment nomornya
        if ($lastNumber) {
            $parts = explode('/', $lastNumber->reference_number);
            $number = intval($parts[0]) + 1;
        }
        // Format nomor dengan padding nol di depan (3 digit)
        $formattedNumber = sprintf('%03d', $number);
        // Format: 001/KODE-SURAT/XII/2024
        return sprintf('%s/%s/%s/%s', $formattedNumber, $prefix, $romanMonth, $year);
    }

    public function reject(Request $request, LetterRequest $letterRequest)
    {
        try {
            $validated = $request->validate([
                'rejection_reason' => 'required|string|min:10',
            ]);

            $letterRequest->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'completed_at' => now()
            ]);

            return redirect()->route('admin.letter-requests.index')->with('success', 'Surat berhasil ditolak.');
        } catch (\Exception $e) {
            Log::error('Error rejecting letter request:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['rejection_reason' => 'Terjadi kesalahan saat menolak surat']);
        }
    }
    public function download(LetterRequest $letterRequest)
    {
        if (!$letterRequest->document_path || !Storage::disk('public')->exists($letterRequest->document_path)) {
            return back()->with('error', 'Dokumen belum tersedia atau tidak ditemukan.');
        }
        return Storage::disk('public')->download($letterRequest->document_path);
    }
}
