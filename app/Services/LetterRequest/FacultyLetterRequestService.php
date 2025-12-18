<?php

namespace App\Services\LetterRequest;

use App\Models\User;
use BaconQrCode\Writer;
use App\Models\TipeSurat;
use App\Models\AcademicYear;
use App\Models\LetterRequest;
use App\Models\LetterType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use BaconQrCode\Renderer\ImageRenderer;
use Illuminate\Support\Facades\Storage;
use App\Notifications\LetterStatusUpdated;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

class FacultyLetterRequestService
{
    public function generatePdf(LetterRequest $letterRequest, $faculty, $student, array $additionalData = [])
    {
        // Fetch assignment from letter_type_assignments
        $assignment = \App\Models\LetterTypeAssignment::where('letter_type_id', $letterRequest->letter_type_id)->first();
        $tipeSurat = $letterRequest->letterType;

        if (!$assignment || !$assignment->is_active || empty($assignment->template_name)) {
            \Log::error('Template surat tidak tersedia', [
                'letter_id' => $letterRequest->id,
                'letter_type_id' => $letterRequest->letter_type_id,
                'assignment_exists' => $assignment ? true : false,
                'is_active' => $assignment->is_active ?? null,
                'template_name' => $assignment->template_name ?? null,
            ]);
            throw new \Exception('Template surat tidak tersedia.');
        }

        $templatePath = 'admin.letters.template.' . str_replace('_', '-', trim($assignment->template_name, '.'));
        \Log::info('Attempting to render PDF view', [
            'template_path' => $templatePath,
            'letter_id' => $letterRequest->id,
            'template_name' => $assignment->template_name,
        ]);

        if (!view()->exists($templatePath)) {
            \Log::error('View not found', [
                'template_path' => $templatePath,
                'letter_id' => $letterRequest->id,
                'template_name' => $assignment->template_name,
            ]);
            throw new \Exception("View [{$templatePath}] not found.");
        }

        $logoUri = base64_encode(file_get_contents(public_path('images/logo_unilaki.png')));
        $logoUri2 = base64_encode(file_get_contents(public_path('images/tutwuri.png')));

        $approvedBy = $letterRequest->approvedBy;
        $lecturer = $approvedBy ? $approvedBy->lecturer : null;
        $date = 'Unaaha, ' . now()->format('d F Y');
        $deanName = $approvedBy ? $approvedBy->name : 'Nama Dekan Tidak Tersedia';
        $deanNip = $lecturer ? $lecturer->nidn : ($approvedBy ? ($approvedBy->nip ?? 'NIP Tidak Tersedia') : 'NIP Tidak Tersedia');
        $referenceNumber = $letterRequest->reference_number ?? 'No Reference';

        $formData = $letterRequest->form_data ?? [];
        $isForDosen = $tipeSurat->for_dosen ?? false;

        // Sesuaikan data berdasarkan tipe surat (dosen atau mahasiswa)
        $userName = $formData['Nama'] ?? 'Nama Tidak Tersedia';
        $programStudy = $formData['Prodi'] ?? 'Program Studi Tidak Tersedia';
        $letterId = $letterRequest->id;

        if ($isForDosen) {
            // Untuk dosen
            $idNumberLabel = 'NIDN';
            $idNumber = $formData['Nidn'] ?? 'NIDN Tidak Tersedia';
            $additionalFieldLabel = 'Judul';
            $additionalField = $formData['Judul'] ?? 'Judul Tidak Tersedia';
        } else {
            // Untuk mahasiswa
            $idNumberLabel = 'NIM';
            $idNumber = $formData['Nim'] ?? 'NIM Tidak Tersedia';
            $additionalFieldLabel = 'Peruntukan';
            $additionalField = $formData['Peruntukan'] ?? 'Tujuan Tidak Tersedia';
        }

        // Generate QR code data
        $barcodeData = "Dekan: " . str_replace(' ', '', $deanName) . PHP_EOL .
            "NIP: {$deanNip}" . PHP_EOL .
            "Date: {$date}" . PHP_EOL .
            "No: {$referenceNumber}" . PHP_EOL .
            "Nama: {$userName}" . PHP_EOL .
            "{$idNumberLabel}: {$idNumber}" . PHP_EOL .
            "Prodi: {$programStudy}" . PHP_EOL .
            "{$additionalFieldLabel}: {$additionalField}" . PHP_EOL .
            "Id: {$letterId}";

        $renderer = new ImageRenderer(new RendererStyle(300), new SvgImageBackEnd());
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($barcodeData);
        $barcodeImage = base64_encode($qrCodeSvg);

        $academicYear = AcademicYear::where('status', 1)->first()?->ta ?? 'Tidak Ada Tahun Akademik Aktif';

        $data = array_merge([
            'user' => $letterRequest->user->name,
            'type' => $tipeSurat->name,
            'reference_number' => $referenceNumber,
            'date' => now()->format('d F Y'),
            'form_data' => $formData,
            'academic_year' => $academicYear,
            'letterRequest' => $letterRequest,
            'dean_name' => $deanName,
            'dean_nip' => $deanNip,
            'logoUri' => $logoUri,
            'logoUri2' => $logoUri2,
            'barcodeImage' => $barcodeImage,
        ], $additionalData);

        return Pdf::loadView($templatePath, $data);
    }

    public function generateReferenceNumber(LetterRequest $letterRequest)
    {
        $prefix = $letterRequest->LetterType->code ?? 'LTR';
        $year = date('Y');
        $month = date('m');
        $romanMonths = ['01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV', '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII', '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'];
        $romanMonth = $romanMonths[$month];

        $lastNumber = LetterRequest::where('reference_number', 'like', "%/{$prefix}/{$romanMonth}/{$year}")
            ->orderBy('reference_number', 'desc')
            ->first();
        $number = $lastNumber ? (int) explode('/', $lastNumber->reference_number)[0] + 1 : 1;
        $formattedNumber = sprintf('%03d', $number);

        return "{$formattedNumber}/{$prefix}/{$romanMonth}/{$year}";
    }

    public function download(LetterRequest $letterRequest)
    {
        if (!$letterRequest->document_path || !Storage::disk('public')->exists($letterRequest->document_path)) {
            throw new \Exception('Dokumen belum tersedia atau tidak ditemukan.');
        }
        return Storage::disk('public')->download($letterRequest->document_path);
    }

    protected function updateApprovalHistory(LetterRequest $letterRequest, string $action, User $user)
    {
        $history = $letterRequest->approval_history ?? [];

        if (!is_array($history)) {
            Log::error('Approval history is not an array', ['history' => $history]);
            $history = [];
        }

        $history[] = [
            'step' => $action,
            'user_id' => $user->id,
            'timestamp' => now()->toDateTimeString(),
        ];

        $letterRequest->approval_history = $history;
        $letterRequest->save();
    }


    public function approve(LetterRequest $letterRequest, User $user, ?string $notes = null)
    {
        $this->validateStep($letterRequest, 'approve', $user);

        $flow = $letterRequest->approval_flow;
        $flow['approve'] = 'approved';
        $history = $letterRequest->approval_history ?? [];
        $history[] = [
            'step' => 'approve',
            'user_id' => $user->id,
            'timestamp' => now()->toDateTimeString(),
        ];

        $letterRequest->update([
            'status' => 'approved',
            'approval_flow' => $flow,
            'approval_history' => $history,
            'notes' => $notes,
            'approved_by' => $user->id,
            'approved_at' => now(),
            'completed_at' => now(),
        ]);

        $this->updateApprovalHistory($letterRequest, 'approve', $user);

        if (empty($letterRequest->reference_number)) {
            $letterRequest->reference_number = $this->generateReferenceNumber($letterRequest);
        }

        if (empty($letterRequest->document_path)) {
            $faculty = $letterRequest->user->student ? $letterRequest->user->student->department->faculty : $letterRequest->user->lecturer->department->faculty;
            $student = $letterRequest->user->student ?? null;
            $pdf = $this->generatePdf($letterRequest, $faculty, $student);
            $pdfPath = 'public/letters/' . $letterRequest->id . '.pdf';
            Storage::put($pdfPath, $pdf->output());
            $letterRequest->document_path = str_replace('public/', '', $pdfPath);
        }

        $letterRequest->save();
    }

    public function review(LetterRequest $letterRequest, User $user)
    {
        $this->validateStep($letterRequest, 'review', $user);

        $approvalFlow = $letterRequest->approval_flow;
        $approvalFlow['review'] = 'approved';

        $letterRequest->update([
            'status' => 'processing',
            'approval_flow' => $approvalFlow,
        ]);

        $this->updateApprovalHistory($letterRequest, 'review', $user);
    }

    public function reject(LetterRequest $letterRequest, User $user, string $reason)
    {
        $this->validateStep($letterRequest, 'reject', $user);

        $approvalFlow = $letterRequest->approval_flow;
        $history = $letterRequest->approval_history ?? []; // Pastikan history tidak null
        $currentStep = $this->getCurrentStep($approvalFlow, $history) ?? ['step' => 'review'];
        $approvalFlow[$currentStep['step']] = 'rejected';

        $letterRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'completed_at' => now(),
            'approval_flow' => $approvalFlow,
        ]);

        $this->updateApprovalHistory($letterRequest, 'reject', $user);
    }

    protected function validateStep(LetterRequest $letterRequest, string $action, User $user)
    {
        $flow = $letterRequest->approval_flow;
        $history = $letterRequest->approval_history ?? [];

        $currentStep = $this->getCurrentStep($flow, $history);

        if (!$currentStep) {
            if ($action === 'reject') {
                $currentStep = ['step' => 'review', 'role' => $this->getRoleForStep('review')];
            } elseif ($action === 'review') {
                if (!isset($flow['review']) || $flow['review'] !== 'pending') {
                    Log::warning('Review step missing or not pending', ['flow' => $flow]);
                    $currentStep = ['step' => 'review', 'role' => $this->getRoleForStep('review')];
                } else {
                    throw new \Exception("Tidak ada langkah berikutnya untuk {$action}.");
                }
            } else {
                throw new \Exception("Tidak ada langkah berikutnya untuk {$action}.");
            }
        }

        if ($action === 'reject' && in_array($currentStep['step'], ['review', 'approve'])) {
            if (!$this->userCanPerformStep($user, $currentStep)) {
                throw new \Exception("Anda tidak memiliki izin untuk melakukan {$action}.");
            }
            return;
        }

        if ($currentStep['step'] !== $action) {
            throw new \Exception("Langkah saat ini bukan {$action}.");
        }

        if (!$this->userCanPerformStep($user, $currentStep)) {
            Log::warning("User does not have required role", ['user_id' => $user->id, 'required_role' => $currentStep['role']]);
            throw new \Exception("Anda tidak memiliki izin untuk melakukan {$action}.");
        }
    }
    protected function getCurrentStep(?array $flow, array $history): ?array
    {
        if (is_null($flow) || empty($flow)) {
            Log::warning('Flow is null or empty');
            return null;
        }

        $currentStep = null;
        foreach ($flow as $step => $status) {
            if ($status === 'pending') {
                $currentStep = $step;
                break;
            }
        }
        if (!$currentStep) {
            Log::warning('No pending step found', ['flow' => $flow]);
            return null;
        }
        return [
            'step' => $currentStep,
            'role' => $this->getRoleForStep($currentStep),
        ];
    }
    protected function getRoleForStep(string $step)
    {
        $roleMap = [
            'submit' => ['dosen', 'mahasiswa'],
            'review' => 'kaprodi',
            'approve' => 'dekan',
        ];

        return $roleMap[$step] ?? 'unknown';
    }

    protected function userCanPerformStep(User $user, array $step)
    {
        $requiredRoles = is_array($step['role']) ? $step['role'] : [$step['role']];
        $userRoles = $user->roles()->pluck('name')->toArray();

        $canPerform = !empty(array_intersect($requiredRoles, $userRoles));

        if (!$canPerform) {
            Log::warning("User does not have required role", ['user_id' => $user->id, 'required_roles' => $requiredRoles]);
        }

        return $canPerform;
    }

    protected function getNextStep(array $flow, string $currentStep, string $action)
    {
        $steps = $flow['steps'];

        $currentIndex = array_search($currentStep, array_column($steps, 'step'));

        if ($currentIndex === false) {
            return $steps[0];
        }

        for ($i = $currentIndex + 1; $i < count($steps); $i++) {
            if ($steps[$i]['action'] === $action || ($action === 'reject' && in_array($steps[$i]['step'], ['review', 'approve']))) {
                return $steps[$i];
            }
            if (array_key_exists('optional', $steps[$i]) && !$steps[$i]['optional']) {
                break;
            }
        }
        return null;
    }

    public function createLetterRequest(array $data, $user)
    {
        $letterType = LetterType::findOrFail($data['letter_type_id']);
        $approvalFlowSteps = $letterType->approval_flow['steps'] ?? [
            ['step' => 'submit'],
            ['step' => 'review'],
            ['step' => 'approve']
        ];

        $approvalFlow = [];
        foreach ($approvalFlowSteps as $stepData) {
            $step = $stepData['step'];
            $approvalFlow[$step] = $step === 'submit' ? 'approved' : 'pending';
        }

        $letterRequest = $user->letterRequests()->create([
            'letter_type_id' => $data['letter_type_id'],
            'form_data' => $data['form_data'],
            'status' => 'submitted',
            'approval_flow' => $approvalFlow,
            'approval_history' => [
                [
                    'step' => 'submit',
                    'user_id' => $user->id,
                    'timestamp' => now()->toDateTimeString(),
                ]
            ]
        ]);

        $letterRequest->user->notify(new LetterStatusUpdated($letterRequest));
        return $letterRequest;
    }
}