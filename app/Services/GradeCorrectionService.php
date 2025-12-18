<?php

namespace App\Services;

use App\Models\User;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\GradeCorrectionRequest;
use BaconQrCode\Renderer\ImageRenderer;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use App\Notifications\GradeCorrectionStatusUpdated;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

class GradeCorrectionService
{
    public function createRequest(array $data, User $user): GradeCorrectionRequest
    {
        $approvalFlow = [
            'submit' => 'approved',
            'staff_review' => 'pending',
            'dosen_process' => 'pending',
            'staff_validate' => 'pending',
            'kaprodi_approve' => 'pending',
        ];

        $request = GradeCorrectionRequest::create([
            'user_id' => $user->id,
            'course_id' => $data['course_id'],
            'current_grade' => $data['current_grade'],
            'semester' => $data['semester'],
            'status' => 'submitted',
            'approval_flow' => $approvalFlow,
            'approval_history' => [
                [
                    'step' => 'submit',
                    'user_id' => $user->id,
                    'timestamp' => now()->toDateTimeString(),
                ],
            ],
        ]);

        $user->notify(new GradeCorrectionStatusUpdated($request));
        return $request;
    }

    public function reviewByStaff(GradeCorrectionRequest $request, User $user): GradeCorrectionRequest
    {
        $this->validateStep($request, 'staff_review', $user);

        $approvalFlow = $request->approval_flow;
        $approvalFlow['staff_review'] = 'approved';
        $request->approval_flow = $approvalFlow;
        $request->status = 'processing';

        $this->updateApprovalHistory($request, 'staff_review', $user);
        $request->save();

        $request->user->notify(new GradeCorrectionStatusUpdated($request));
        return $request;
    }

    public function processByDosen(GradeCorrectionRequest $request, User $user, array $updatedData): GradeCorrectionRequest
    {
        Log::info('Validating step for dosen processing', ['request_id' => $request->id, 'user_id' => $user->id]);
        $this->validateStep($request, 'dosen_process', $user); // Perbaiki dari 'processing' ke 'dosen_process'

        Log::info('Updating request data', ['request_id' => $request->id, 'updated_data' => $updatedData]);
        $request->update(array_merge(['status' => 'validated'], $updatedData));
        $approvalFlow = $request->approval_flow;
        $approvalFlow['dosen_process'] = 'approved';
        $request->approval_flow = $approvalFlow;

        Log::info('Updating approval history', ['request_id' => $request->id]);
        $this->updateApprovalHistory($request, 'dosen_process', $user);
        $request->save();

        Log::info('Notifying user', ['request_id' => $request->id, 'user_id' => $request->user_id]);
        $request->user->notify(new GradeCorrectionStatusUpdated($request));

        return $request;
    }

    public function validateByStaff(GradeCorrectionRequest $request, User $user): GradeCorrectionRequest
    {
        $this->validateStep($request, 'staff_validate', $user);

        $approvalFlow = $request->approval_flow;
        $approvalFlow['staff_validate'] = 'approved';
        $request->approval_flow = $approvalFlow;
        $request->status = 'pending_kaprodi';

        $this->updateApprovalHistory($request, 'staff_validate', $user);
        $request->save();

        $request->user->notify(new GradeCorrectionStatusUpdated($request));
        return $request;
    }

    public function approveByKaprodi(GradeCorrectionRequest $request, User $user): GradeCorrectionRequest
    {
        $this->validateStep($request, 'kaprodi_approve', $user);

        $approvalFlow = $request->approval_flow;
        $approvalFlow['kaprodi_approve'] = 'approved';
        $request->approval_flow = $approvalFlow;
        $request->status = 'approved';
        $request->approved_by = $user->id;

        $this->updateApprovalHistory($request, 'kaprodi_approve', $user);
        $request->save();

        // Generate PDF (opsional)
        $pdf = $this->generatePdf($request);
        $pdfPath = 'public/grade_corrections/' . $request->id . '.pdf';
        Storage::put($pdfPath, $pdf->output());
        $request->document_path = str_replace('public/', '', $pdfPath);
        $request->save();

        $request->user->notify(new GradeCorrectionStatusUpdated($request));
        return $request;
    }

    public function getHistoryRequests(User $user)
    {
        $historyRequests = collect();
        $departmentId = $this->getDepartmentId($user);
        $historyRequests = collect();
        $departmentId = $this->getDepartmentId($user);
        if ($user->hasRole('mahasiswa')) {
            $historyRequests = GradeCorrectionRequest::where('user_id', $user->id)
                ->whereIn('status', ['approved', 'rejected'])
                ->get();
        } elseif ($user->hasRole('dosen')) {
            $lecturerId = $user->lecturer->id;
            $historyRequests = GradeCorrectionRequest::whereHas('course.schedules', function ($query) use ($lecturerId) {
                $query->whereHas('lecturersInSchedule', function ($subQuery) use ($lecturerId) {
                    $subQuery->where('lecturer_id', $lecturerId);
                });
            })
                ->whereIn('status', ['validated', 'pending_kaprodi', 'approved', 'rejected'])
                ->whereHas('user.student', function ($query) use ($departmentId) {
                    $query->where('department_id', $departmentId);
                })
                ->get();
        } elseif ($user->hasRole('staff')) {
            $historyRequests = GradeCorrectionRequest::whereIn('status', ['validated', 'pending_kaprodi', 'approved', 'rejected'])
                ->whereHas('user.student', function ($query) use ($departmentId) {
                    $query->where('department_id', $departmentId);
                })
                ->get();
        } elseif ($user->hasRole('kaprodi')) {
            if (!$departmentId) {
                throw new \Exception("Department ID untuk kaprodi tidak ditemukan untuk user {$user->id}");
            }
            $validUserIds = DB::table('students')
                ->where('department_id', $departmentId)
                ->pluck('user_id');
            Log::info("Valid user IDs for dept {$departmentId} for user {$user->id}: " . json_encode($validUserIds->toArray()));

            $query = GradeCorrectionRequest::whereIn('status', ['approved', 'rejected'])
                ->whereIn('user_id', $validUserIds);
            Log::info("History SQL Query for user {$user->id}: " . $query->toSql() . " with bindings: " . json_encode($query->getBindings()));

            $historyRequests = $query->get();
            Log::info("History requests fetched for kaprodi user {$user->id}: " . json_encode($historyRequests->toArray()));
        }

        return $historyRequests->load('user', 'course');
    }

    public function getOngoingRequests(User $user)
    {
        $ongoingRequests = collect();
        $departmentId = $this->getDepartmentId($user);
        $ongoingRequests = collect();
        $departmentId = $this->getDepartmentId($user);
        if ($user->hasRole('mahasiswa')) {
            $ongoingRequests = GradeCorrectionRequest::where('user_id', $user->id)
                ->whereIn('status', ['submitted', 'processing', 'validated', 'pending_kaprodi'])
                ->get();
        } elseif ($user->hasRole('dosen')) {
            $lecturerId = $user->lecturer->id;
            $ongoingRequests = GradeCorrectionRequest::whereHas('course.schedules', function ($query) use ($lecturerId) {
                $query->whereHas('lecturersInSchedule', function ($subQuery) use ($lecturerId) {
                    $subQuery->where('lecturer_id', $lecturerId);
                });
            })
                ->where('status', 'processing')
                ->whereHas('user.student', function ($query) use ($departmentId) {
                    $query->where('department_id', $departmentId);
                })
                ->get();
        } elseif ($user->hasRole('staff')) {
            $ongoingRequests = GradeCorrectionRequest::whereIn('status', ['submitted', 'processing', 'validated', 'pending_kaprodi'])
                ->whereHas('user.student', function ($query) use ($departmentId) {
                    $query->where('department_id', $departmentId);
                })
                ->get();

        } elseif ($user->hasRole('kaprodi')) {
            if (!$departmentId) {
                throw new \Exception("Department ID untuk kaprodi tidak ditemukan untuk user {$user->id}");
            }
            $validUserIds = DB::table('students')
                ->where('department_id', $departmentId)
                ->pluck('user_id');
            Log::info("Valid user IDs for dept {$departmentId} for user {$user->id}: " . json_encode($validUserIds->toArray()));

            $query = GradeCorrectionRequest::where('status', 'pending_kaprodi')
                ->whereIn('user_id', $validUserIds);
            Log::info("Ongoing SQL Query for user {$user->id}: " . $query->toSql() . " with bindings: " . json_encode($query->getBindings()));

            $ongoingRequests = $query->get();
            Log::info("Ongoing requests fetched for kaprodi user {$user->id}: " . json_encode($ongoingRequests->toArray()));
        }

        return $ongoingRequests->load('user', 'course');
    }

    protected function getDepartmentId(User $user)
    {
        if ($user->hasRole('staff')) {
            return $user->employee->department_id;
        } elseif ($user->hasRole('dosen')) {
            return $user->lecturer->department_id;
        } elseif ($user->hasRole('kaprodi')) {
            return $user->lecturer->department_id;
        }
        return null;
    }

    public function getRequestDetails(GradeCorrectionRequest $request, User $user)
    {
        $isAuthorized = false;

        if ($user->hasRole('mahasiswa')) {
            $isAuthorized = $request->user_id === $user->id;
        } elseif ($user->hasRole('dosen')) {
            $lecturerId = $user->lecturer->id;
            $isAuthorized = GradeCorrectionRequest::where('id', $request->id)
                ->whereHas('course.schedules', function ($query) use ($lecturerId) {
                    $query->whereHas('lecturersInSchedule', function ($subQuery) use ($lecturerId) {
                        $subQuery->where('lecturer_id', $lecturerId);
                    });
                })
                ->exists();
        } elseif ($user->hasRole('staff')) {
            $isAuthorized = true;
        } elseif ($user->hasRole('kaprodi')) {
            $isAuthorized = in_array($request->status, ['pending_kaprodi', 'approved', 'rejected']);
        }

        if (!$isAuthorized) {
            throw new \Exception('Unauthorized access to request details.');
        }

        $schedule = $request->course->schedules()->orderBy('created_at', 'desc')->first();
        $lecturers = $schedule ? $schedule->lecturersInSchedule : collect();

        return [
            'request' => $request->load('user', 'course'),
            'lecturers' => $lecturers,
        ];
    }

    protected function validateStep(GradeCorrectionRequest $request, string $action, User $user)
    {
        $flow = $request->approval_flow;
        $history = $request->approval_history ?? [];

        $currentStep = $this->getCurrentStep($flow, $history);
        if (!$currentStep || $currentStep['step'] !== $action) {
            throw new \Exception("Langkah saat ini bukan {$action}.");
        }

        if (!$this->userCanPerformStep($user, $currentStep)) {
            throw new \Exception("Anda tidak memiliki izin untuk melakukan {$action}.");
        }
    }

    protected function getCurrentStep(array $flow, array $history)
    {
        foreach ($flow as $step => $status) {
            if ($status === 'pending') {
                return ['step' => $step, 'role' => $this->getRoleForStep($step)];
            }
        }
        return null;
    }

    protected function getRoleForStep(string $step)
    {
        return match ($step) {
            'submit' => 'mahasiswa',
            'staff_review', 'staff_validate' => 'staff',
            'dosen_process' => 'dosen',
            'kaprodi_approve' => 'kaprodi',
            default => 'unknown',
        };
    }

    protected function userCanPerformStep(User $user, array $step)
    {
        $requiredRoles = [$step['role']];
        $userRoles = $user->roles()->pluck('name')->toArray();
        return !empty(array_intersect($requiredRoles, $userRoles));
    }

    protected function updateApprovalHistory(GradeCorrectionRequest $request, string $action, User $user)
    {
        $history = $request->approval_history ?? [];
        $history[] = [
            'step' => $action,
            'user_id' => $user->id,
            'timestamp' => now()->toDateTimeString(),
        ];
        $request->approval_history = $history;
    }

    protected function generatePdf(GradeCorrectionRequest $request)
    {
        $templatePath = 'remedial.pdf';
        $logoUri = base64_encode(file_get_contents(public_path('images/logo_unilaki.png')));

        // Siapkan data untuk QR code
        $barcodeData = "ID: {$request->id}" . PHP_EOL .
            "Mahasiswa: {$request->user->name}" . PHP_EOL .
            "Mata Kuliah: {$request->course->name}" . PHP_EOL .
            "Semester: {$request->semester}" . PHP_EOL .
            "Nilai Lama: {$request->current_grade}" . PHP_EOL .
            "Nilai Baru: {$request->requested_grade}" . PHP_EOL .
            "Tanggal Persetujuan: " . now()->format('d F Y');

        $renderer = new ImageRenderer(
            new RendererStyle(125),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($barcodeData);
        $barcodeImage = base64_encode($qrCodeSvg);

        $data = [
            'request' => $request,
            'user' => $request->user,
            'logoUri' => $logoUri,
            'barcodeImage' => $barcodeImage,
        ];

        return Pdf::loadView($templatePath, $data);
    }
}
