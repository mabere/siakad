<?php

namespace App\Http\Controllers\Surat;

use App\Models\User;
use App\Models\letterType;
use Illuminate\Http\Request;
use App\Models\LetterRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\LetterTypeAssignment;
use Illuminate\Support\Facades\Auth;
use App\Notifications\LetterStatusUpdated;
use App\Services\LetterRequest\FacultyLetterRequestService;

class LetterRequestByMhsController extends Controller
{
    protected $service;

    public function __construct(FacultyLetterRequestService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $surat = auth()->user()->letterRequests()
            ->with('letterType')
            ->latest()
            ->paginate(10);

        return view('letter-request.mhs.index', compact('surat'));
    }

    public function create()
    {
        $user = Auth::user()->load('student.department');

        // Validate user permissions
        if (!$user->hasRole('mahasiswa') || !$user->student || !$user->student->department || !$user->student->department->faculty_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengajukan surat.');
        }

        $facultyId = $user->student->department->faculty_id;
        $departmentId = $user->student->department->id;
        // $letterType = LetterType::where('for_dosen', 0)
        //     ->join('letter_type_assignments', 'letter_types.id', '=', 'letter_type_assignments.letter_type_id')
        //     ->whereNotNull('letter_type_assignments.faculty_id')
        //     ->whereNull('letter_type_assignments.department_id')
        //     ->where('letter_type_assignments.faculty_id', $facultyId)
        //     ->select('letter_types.*') // Avoid duplicate columns
        //     ->distinct()
        //     ->get();
        $letterType = LetterType::where('for_dosen', 0)
            ->join('letter_type_assignments', 'letter_types.id', '=', 'letter_type_assignments.letter_type_id')
            ->where(function ($query) use ($facultyId, $departmentId) {
                $query->where('letter_type_assignments.faculty_id', $facultyId)
                    ->whereNull('letter_type_assignments.department_id')
                    ->orWhere('letter_type_assignments.department_id', $departmentId);
            })
            ->select('letter_types.*')
            ->distinct()
            ->get();

        return view('letter-request.mhs.create', compact('letterType'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'letter_type_id' => 'required|exists:letter_types,id',
            'form_data' => 'required|array',
        ]);

        $letterType = LetterType::findOrFail($request->letter_type_id);

        if ($letterType->for_dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis surat ini tidak tersedia untuk mahasiswa.',
            ], 403);
        }

        // Fetch approval_flow from letter_type_assignments
        $assignment = LetterTypeAssignment::where('letter_type_id', $request->letter_type_id)->first();
        if (!$assignment || !$assignment->approval_flow) {
            return response()->json([
                'success' => false,
                'message' => 'Approval flow tidak ditemukan untuk jenis surat ini.',
            ], 400);
        }

        $status = $request->input('status', 'submitted');
        $approvalFlow = [];
        foreach ($assignment->approval_flow['steps'] as $step) {
            $approvalFlow[$step['step']] = $step['step'] === 'submit' ? 'approved' : 'pending';
        }

        try {
            $letterRequest = auth()->user()->letterRequests()->create([
                'letter_type_id' => $request->letter_type_id,
                'form_data' => $request->form_data,
                'status' => $status,
                'approval_flow' => $status === 'draft' ? [] : $approvalFlow,
                'approval_history' => $status === 'draft' ? [] : [
                    [
                        'step' => 'submit',
                        'user_id' => Auth::id(),
                        'timestamp' => now()->toDateTimeString(),
                    ]
                ]
            ]);

            if ($status === 'submitted') {
                $letterRequest->user->notify(new LetterStatusUpdated($letterRequest));
            }

            $message = $status === 'draft' ? 'Draft berhasil disimpan.' : 'Surat berhasil diajukan.';
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('student.request.surat.index')
            ]);

        } catch (\Exception $e) {
            \Log::error('Error storing letter request:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan surat.',
            ], 500);
        }
    }

    public function edit(LetterRequest $letterRequest)
    {
        $this->authorize('update', $letterRequest);
        $letterRequest->load('user');

        if (!$letterRequest->isEditable()) {
            return redirect()->route('student.request.letter.index')
                ->with('error', 'This letter request cannot be edited.');
        }

        $letterTypes = LetterType::all();
        return view('letter-request.mhs.edit', compact('letterRequest', 'letterTypes'));
    }

    public function update(Request $request, LetterRequest $letterRequest)
    {
        $this->authorize('update', $letterRequest);

        $validated = $request->validate([
            'form_data' => 'required|array',
            'status' => 'required|in:draft,submitted'
        ]);

        try {
            $approvalFlow = $letterRequest->approval_flow;
            $approvalHistory = $letterRequest->approval_history ?? [];

            // Jika status berubah ke submitted dan approval_flow kosong
            if ($validated['status'] === 'submitted' && empty($approvalFlow)) {
                $assignment = \App\Models\LetterTypeAssignment::where('letter_type_id', $letterRequest->letter_type_id)->first();
                if (!$assignment || !$assignment->approval_flow) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Approval flow tidak ditemukan untuk jenis surat ini.',
                    ], 400);
                }

                foreach ($assignment->approval_flow['steps'] as $step) {
                    $approvalFlow[$step['step']] = $step['step'] === 'submit' ? 'approved' : 'pending';
                }
                $approvalHistory[] = [
                    'step' => 'submit',
                    'user_id' => Auth::id(),
                    'timestamp' => now()->toDateTimeString(),
                ];
            }

            // Jika status menjadi draft, kosongkan approval_flow dan history
            if ($validated['status'] === 'draft') {
                $approvalFlow = [];
                $approvalHistory = [];
            }

            $letterRequest->update([
                'form_data' => $validated['form_data'],
                'status' => $validated['status'],
                'approval_flow' => $approvalFlow,
                'approval_history' => $approvalHistory
            ]);

            if ($validated['status'] === 'submitted') {
                $letterRequest->user->notify(new \App\Notifications\LetterStatusUpdated($letterRequest));
            }

            $message = $validated['status'] === 'draft'
                ? 'Permintaan surat disimpan sebagai draft'
                : 'Permintaan surat berhasil diperbarui dan diajukan';

            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('student.request.surat.show', $letterRequest)
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating letter request:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'letter_request_id' => $letterRequest->id,
                'status' => $validated['status']
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui surat: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(LetterRequest $letterRequest)
    {
        $this->authorize('view', $letterRequest);

        // Refresh data notifikasi sebelum cek
        if (auth()->check()) {
            auth()->user()->load('unreadNotifications');
        }

        // Tandai notifikasi sebagai dibaca
        $notification = auth()->user()->unreadNotifications()
            ->where('data->letter_id', $letterRequest->id)
            ->first();
        if ($notification) {
            $notification->markAsRead();
            // Refresh lagi setelah markAsRead
            auth()->user()->load('unreadNotifications');
        }

        $letterRequest->load('letterType', 'user');

        $userIds = collect($letterRequest->approval_history)->pluck('user_id')->unique();
        $users = User::whereIn('id', $userIds)->get()->pluck('name', 'id');
        $historyWithNames = collect($letterRequest->approval_history)->map(function ($history) use ($users) {
            $history['user_name'] = $users[$history['user_id']] ?? 'User Tidak Ditemukan';
            return $history;
        });
        $letterRequest->approval_history = $historyWithNames->toArray();

        return view('letter-request.mhs.show', compact('letterRequest'));
    }

    public function destroy(LetterRequest $letterRequest)
    {
        $this->authorize('delete', $letterRequest);
        if (!in_array($letterRequest->status, ['draft', 'submitted'])) {
            return back()->with('error', 'Surat tidak dapat dibatalkan');
        }
        $letterRequest->forceDelete();
        return redirect()->route('student.request.surat.index')->with('success', 'Pengajuan surat berhasil dibatalkan');
    }

    public function download(LetterRequest $letterRequest)
    {
        return $this->service->download($letterRequest);
    }

}