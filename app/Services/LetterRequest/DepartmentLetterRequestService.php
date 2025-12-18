<?php

namespace App\Services\LetterRequest;

use App\Models\LetterRequest;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\LetterType;
use App\Models\TipeSurat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Picqer\Barcode\BarcodeGeneratorPNG;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DepartmentLetterRequestService
{
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

    protected function validateStep(LetterRequest $letterRequest, string $action, User $user)
    {
        $flow = $letterRequest->letterType->approval_flow;
        $history = $letterRequest->approval_history ?? [];

        $currentStep = $this->getCurrentStep($flow, $history);
        Log::info('Current Step', ['step' => $currentStep]);

        if (!$currentStep) {
            throw new \Exception("Tidak ada langkah berikutnya untuk {$action}.");
        }

        if ($action === 'reject' && in_array($currentStep['step'], ['staff_review', 'kaprodi_approve'])) {
            if (!$this->userCanPerformStep($user, $currentStep)) {
                Log::warning("User does not have required role for reject", ['user_id' => $user->id, 'required_role' => $currentStep['role']]);
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

    protected function getCurrentStep(array $flow, array $history)
    {
        $currentStep = null;
        foreach ($flow as $step => $status) {
            if ($status === 'pending') {
                $currentStep = $step;
                break;
            }
        }

        if (!$currentStep && $status === 'processing_by_dosen') {
            $currentStep = 'dosen_process';
        }

        if (!$currentStep) {
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
            'submit' => ['mahasiswa'],
            'staff_review' => 'staff',
            'dosen_process' => 'dosen',
            'staff_validate' => 'staff',
            'kaprodi_approve' => 'kaprodi',
        ];

        return $roleMap[$step] ?? 'unknown';
    }

    protected function userCanPerformStep(User $user, array $step)
    {
        $requiredRoles = is_array($step['role']) ? $step['role'] : [$step['role']];
        $userRoles = $user->roles()->pluck('name')->toArray();

        $canPerform = !empty(array_intersect($requiredRoles, $userRoles));
        Log::info('Can Perform Step', [
            'can_perform' => $canPerform,
            'user_roles' => $userRoles,
            'required_roles' => $requiredRoles
        ]);

        if (!$canPerform) {
            Log::warning("User does not have required role", ['user_id' => $user->id, 'required_roles' => $requiredRoles]);
        }

        return $canPerform;
    }

    public function createLetterRequest(array $data, $user)
    {
        $letterType = LetterType::findOrFail($data['tipe_surat_id']);
        if ($letterType->level !== 'department') {
            throw new \Exception("Tipe surat ini bukan untuk level departemen.");
        }

        $approvalFlowSteps = array_column($letterType->approval_flow['steps'], 'step');
        $approvalFlow = [];
        foreach ($approvalFlowSteps as $step) {
            $approvalFlow[$step] = $step === 'submit' ? 'approved' : 'pending';
        }

        $letterRequest = $user->letterRequests()->create([
            'tipe_surat_id' => $data['tipe_surat_id'],
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

        $letterRequest->user->notify(new \App\Notifications\LetterStatusUpdated($letterRequest));
        return $letterRequest;
    }

    public function reviewByStaff(LetterRequest $letterRequest, User $user)
    {
        Log::info('Starting staff review', ['letter_id' => $letterRequest->id, 'user_id' => $user->id]);
        $this->validateStep($letterRequest, 'staff_review', $user);

        $approvalFlow = $letterRequest->approval_flow;
        $approvalFlow['staff_review'] = 'approved';
        $letterRequest->approval_flow = $approvalFlow;
        $letterRequest->status = 'staff_reviewed';

        $this->updateApprovalHistory($letterRequest, 'staff_review', $user);
        $letterRequest->save();

        $letterRequest->user->notify(new \App\Notifications\LetterStatusUpdated($letterRequest));
    }

    public function reject(LetterRequest $letterRequest, User $user, string $reason)
    {
        $this->validateStep($letterRequest, 'reject', $user);

        $approvalFlow = $letterRequest->approval_flow;
        $currentStep = $this->getCurrentStep($letterRequest->letterType->approval_flow, $letterRequest->approval_history);
        $approvalFlow[$currentStep['step']] = 'rejected';
        $letterRequest->approval_flow = $approvalFlow;

        $letterRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'completed_at' => now(),
        ]);

        $this->updateApprovalHistory($letterRequest, 'reject', $user);
    }

    public function processByDosen(LetterRequest $letterRequest, User $user, array $updatedData)
    {
        Log::info('Starting dosen process', ['letter_id' => $letterRequest->id, 'user_id' => $user->id]);
        $this->validateStep($letterRequest, 'dosen_process', $user);

        $formData = $letterRequest->form_data;
        $formData = array_merge($formData, $updatedData);
        $letterRequest->form_data = $formData;
        $letterRequest->status = 'processed_by_dosen';
        $approvalFlow = $letterRequest->approval_flow;
        $approvalFlow['dosen_process'] = 'approved';
        $letterRequest->approval_flow = $approvalFlow;

        $this->updateApprovalHistory($letterRequest, 'dosen_process', $user);
        $letterRequest->save();

        $letterRequest->user->notify(new \App\Notifications\LetterStatusUpdated($letterRequest));
    }

    public function validateByStaff(LetterRequest $letterRequest, User $user)
    {
        Log::info('Starting staff validate', ['letter_id' => $letterRequest->id, 'user_id' => $user->id]);
        $this->validateStep($letterRequest, 'staff_validate', $user);

        $approvalFlow = $letterRequest->approval_flow;
        $approvalFlow['staff_validate'] = 'approved';
        $letterRequest->approval_flow = $approvalFlow;
        $letterRequest->status = 'pending_kaprodi_approve';

        $this->updateApprovalHistory($letterRequest, 'staff_validate', $user);
        $letterRequest->save();

        $letterRequest->user->notify(new \App\Notifications\LetterStatusUpdated($letterRequest));
    }

    public function approveByKaprodi(LetterRequest $letterRequest, User $user)
    {
        Log::info('Starting kaprodi approve', ['letter_id' => $letterRequest->id, 'user_id' => $user->id]);
        $this->validateStep($letterRequest, 'kaprodi_approve', $user);

        $approvalFlow = $letterRequest->approval_flow;
        $approvalFlow['kaprodi_approve'] = 'approved';
        $letterRequest->approval_flow = $approvalFlow;
        $letterRequest->status = 'completed';

        $this->updateApprovalHistory($letterRequest, 'kaprodi_approve', $user);

        if (empty($letterRequest->reference_number)) {
            $letterRequest->reference_number = $this->generateReferenceNumber($letterRequest);
        }

        if (empty($letterRequest->document_path)) {
            $department = $letterRequest->user->student ? $letterRequest->user->student->department : $letterRequest->user->lecturer->department;
            $student = $letterRequest->user->student ?? null;
            $pdf = $this->generatePdf($letterRequest, $department, $student);
            $pdfPath = 'public/letters/department/' . $letterRequest->id . '.pdf';
            Storage::put($pdfPath, $pdf->output());
            $letterRequest->document_path = str_replace('public/', '', $pdfPath);
        }

        $letterRequest->save();
        $letterRequest->user->notify(new \App\Notifications\LetterStatusUpdated($letterRequest));
    }

    protected function generateReferenceNumber(LetterRequest $letterRequest)
    {
        $department = $letterRequest->user->student ? $letterRequest->user->student->department : $letterRequest->user->lecturer->department;
        $year = now()->format('Y');
        $month = now()->format('m');
        $count = LetterRequest::where('tipe_surat_id', $letterRequest->tipe_surat_id)->count();
        return sprintf("%03d/%s-%s/DEP/%s/%s", $count + 1, $department->code, $letterRequest->tipeSurat->code, $month, $year);
    }

    public function generatePdf(LetterRequest $letterRequest, $department, $student, array $additionalData = [])
    {
        $tipeSurat = $letterRequest->letterType;
        if (!$tipeSurat->active || empty($tipeSurat->template)) {
            Log::error('Template surat tidak tersedia', ['letter_id' => $letterRequest->id]);
            throw new \Exception('Template surat tidak tersedia.');
        }

        $templatePath = 'admin.letters.template.' . str_replace('_', '-', $tipeSurat->template);
        $logoUri = base64_encode(file_get_contents(public_path('images/logo_unilaki.png')));
        $logoUri2 = base64_encode(file_get_contents(public_path('images/tutwuri.png')));

        $approvedBy = $letterRequest->approvedBy;
        $lecturer = $approvedBy ? $approvedBy->lecturer : null;
        $date = 'Unaaha, ' . now()->format('d F Y');
        $deanName = $approvedBy ? $approvedBy->name : 'Nama Kaprodi Tidak Tersedia';
        $deanNip = $lecturer ? $lecturer->nidn : ($approvedBy ? ($approvedBy->nip ?? 'NIP Tidak Tersedia') : 'NIP Tidak Tersedia');
        $referenceNumber = $letterRequest->reference_number ?? 'No Reference';

        $formData = $letterRequest->form_data ?? [];
        $isForDosen = $tipeSurat->for_dosen ?? false;

        $userName = $formData['Nama'] ?? 'Nama Tidak Tersedia';
        $programStudy = $formData['Prodi'] ?? 'Program Studi Tidak Tersedia';
        $letterId = $letterRequest->id;

        if ($isForDosen) {
            $idNumberLabel = 'NIDN';
            $idNumber = $formData['Nidn'] ?? 'NIDN Tidak Tersedia';
            $additionalFieldLabel = 'Judul';
            $additionalField = $formData['Judul'] ?? 'Judul Tidak Tersedia';
        } else {
            $idNumberLabel = 'NIM';
            $idNumber = $formData['Nim'] ?? 'NIM Tidak Tersedia';
            $additionalFieldLabel = 'Mata Kuliah';
            $additionalField = $formData['Mata_Kuliah'] ?? 'Mata Kuliah Tidak Tersedia';
        }

        $barcodeData = "Kaprodi: " . str_replace(' ', '', $deanName) . PHP_EOL .
            "NIP: {$deanNip}" . PHP_EOL .
            "Date: {$date}" . PHP_EOL .
            "No: {$referenceNumber}" . PHP_EOL .
            "Nama: {$userName}" . PHP_EOL .
            "{$idNumberLabel}: {$idNumber}" . PHP_EOL .
            "Prodi: {$programStudy}" . PHP_EOL .
            "{$additionalFieldLabel}: {$additionalField}" . PHP_EOL .
            "Id: {$letterId}";

        $barcodeImage = base64_encode(QrCode::format('png')->size(125)->generate($barcodeData));
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
}