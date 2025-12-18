<?php

namespace App\Http\Controllers\Surat;

use view;
use App\Models\User;
use App\Models\TipeSurat;
use App\Models\LetterType;
use Illuminate\Http\Request;
use App\Models\LetterRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\LetterRequest\FacultyLetterRequestService;

class LetterRequestByDosenController extends Controller
{
    protected $service;

    public function __construct(FacultyLetterRequestService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $letterRequests = auth()->user()->letterRequests()
            ->with('letterType')
            ->latest()
            ->paginate(10);
        return view('letter-request.dosen.index', compact('letterRequests'));
    }

    public function create()
    {
        $user = Auth::user()->load('lecturer.department');

        // Validate user permissions
        if (!$user->hasRole('dosen') || !$user->lecturer || !$user->lecturer->department || !$user->lecturer->department->faculty_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengajukan surat.');
        }

        $facultyId = $user->lecturer->department->faculty_id;
        $departmentId = $user->lecturer->department_id;

        $letterType = LetterType::where('for_dosen', true)
            ->whereExists(function ($subQuery) use ($facultyId, $departmentId) {
                $subQuery->select(DB::raw(1))
                    ->from('letter_type_assignments')
                    ->whereColumn('letter_type_assignments.letter_type_id', 'letter_types.id')
                    ->where(function ($q) use ($facultyId, $departmentId) {
                        $q->where('letter_type_assignments.faculty_id', $facultyId)
                            ->orWhere('letter_type_assignments.department_id', $departmentId);
                    })
                    ->where('letter_type_assignments.is_active', 1);
            })
            ->select('letter_types.*')
            ->distinct()
            ->get();

        return view('letter-request.dosen.create', compact('letterType'));
    }

    public function store(Request $request)
    {
        $user = Auth::user()->load('lecturer.department');

        // Validate user permissions
        if (!$user->hasRole('dosen') || !$user->lecturer || !$user->lecturer->department || !$user->lecturer->department->faculty_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengajukan surat.');
        }

        $facultyId = $user->lecturer->department->faculty_id;
        $departmentId = $user->lecturer->department_id;

        // Validate request
        $validated = $request->validate([
            'letter_type_id' => [
                'required',
                'exists:letter_types,id',
                function ($attribute, $value, $fail) use ($facultyId, $departmentId) {
                    $exists = LetterType::where('id', $value)
                        ->where('for_dosen', true)
                        ->whereExists(function ($subQuery) use ($facultyId, $departmentId) {
                            $subQuery->select(DB::raw(1))
                                ->from('letter_type_assignments')
                                ->whereColumn('letter_type_assignments.letter_type_id', 'letter_types.id')
                                ->where(function ($q) use ($facultyId, $departmentId) {
                                    $q->where('letter_type_assignments.faculty_id', $facultyId)
                                        ->orWhere('letter_type_assignments.department_id', $departmentId);
                                })
                                ->where('letter_type_assignments.is_active', 1);
                        })
                        ->exists();
                    if (!$exists) {
                        $fail('Tipe surat tidak valid untuk fakultas atau departemen Anda.');
                    }
                },
            ],
            'form_data' => 'required|array',
        ]);

        // Create letter request via service
        $letterRequest = $this->service->createLetterRequest($validated, $user);

        $redirectUrl = route('lecturer.request.surat.index');
        $successMsg = 'Pengajuan surat berhasil diajukan.';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'redirect' => $redirectUrl,
            ]);
        }

        return redirect($redirectUrl)->with('success', $successMsg);

        // return redirect()->route('lecturer.request.surat.index')->with('success', 'Pengajuan surat berhasil diajukan.');
    }

    public function show(LetterRequest $letterRequest)
    {
        $this->authorize('view', $letterRequest);

        if (auth()->check()) {
            auth()->user()->load('unreadNotifications');
        }

        $notification = auth()->user()->unreadNotifications()
            ->where('data->letter_id', $letterRequest->id)
            ->first();
        if ($notification) {
            $notification->markAsRead();
            $user = auth()->user()->fresh();
            $user->load('unreadNotifications');
            auth()->setUser($user);
        }

        $letterRequest->load('letterType', 'user');

        $userIds = collect($letterRequest->approval_history)->pluck('user_id')->unique();
        $users = User::whereIn('id', $userIds)->get()->pluck('name', 'id');
        $historyWithNames = collect($letterRequest->approval_history)->map(function ($history) use ($users) {
            $history['user_name'] = $users[$history['user_id']] ?? 'User Tidak Ditemukan';
            return $history;
        });
        $letterRequest->approval_history = $historyWithNames->toArray();

        return view('letter-request.dosen.show', compact('letterRequest'));
    }

    public function download(LetterRequest $letterRequest)
    {
        $pdfPath = storage_path('app/public/' . $letterRequest->document_path);
        if (!file_exists($pdfPath)) {
            return redirect()->back()->with('error', 'File PDF tidak ditemukan.');
        }

        return response()->download($pdfPath, 'surat-tugas-penelitian-' . $letterRequest->id . '.pdf');
    }
}