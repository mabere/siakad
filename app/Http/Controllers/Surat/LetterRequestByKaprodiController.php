<?php

namespace App\Http\Controllers\Surat;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\LetterRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\LetterRequest\FacultyLetterRequestService;

class LetterRequestByKaprodiController extends Controller
{
    protected $service;

    public function __construct(FacultyLetterRequestService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $user = Auth::user()->load('lecturer.department');

        // Validate user permissions
        if (!$user->hasRole('kaprodi') || !$user->lecturer || !$user->lecturer->department || !$user->lecturer->department->faculty_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses daftar ajuan surat.');
        }

        $facultyId = $user->lecturer->department->faculty_id;
        $departmentId = $user->lecturer->department_id;

        $letterRequests = LetterRequest::with(['letterType', 'user'])
            ->whereHas('letterType', function ($query) use ($facultyId, $departmentId) {
                $query->whereExists(function ($subQuery) use ($facultyId, $departmentId) {
                    $subQuery->select(\DB::raw(1))
                        ->from('letter_type_assignments')
                        ->whereColumn('letter_type_assignments.letter_type_id', 'letter_types.id')
                        ->where(function ($q) use ($facultyId, $departmentId) {
                            $q->where('letter_type_assignments.faculty_id', $facultyId)
                                ->orWhere('letter_type_assignments.department_id', $departmentId);
                        });
                });
            })
            ->paginate(10);

        return view('letter-request.kaprodi.index', ['ajuanSurat' => $letterRequests]);
    }


    public function show(LetterRequest $letterRequest)
    {
        $this->authorize('view', $letterRequest);

        $letterRequest->load('user', 'user.student.department', 'letterType');
        $userIds = collect($letterRequest->approval_history)->pluck('user_id')->unique();
        $users = User::whereIn('id', $userIds)->get()->pluck('name', 'id');
        $historyWithNames = collect($letterRequest->approval_history)->map(function ($history) use ($users) {
            $history['user_name'] = $users[$history['user_id']] ?? 'User Tidak Ditemukan';
            return $history;
        });
        $letterRequest->approval_history = $historyWithNames->toArray();
        return view('letter-request.kaprodi.show', compact('letterRequest'));
    }


    // Pembaruan kolom approval_flow
    public function review(Request $request, LetterRequest $letterRequest)
    {
        $this->authorize('review', $letterRequest);
        try {
            $this->service->review($letterRequest, Auth::user());
            $letterRequest->user->notify(new \App\Notifications\LetterStatusUpdated($letterRequest));
            return redirect()->route('kaprodi.request.surat-masuk.index')->with('success', 'Surat telah direview.');
        } catch (\Exception $e) {
            Log::error('Error reviewing letter request: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, LetterRequest $letterRequest)
    {
        $this->authorize('review', $letterRequest); // Kaprodi punya hak review di tahap submitted

        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        try {
            $this->service->reject($letterRequest, Auth::user(), $request->rejection_reason);
            $letterRequest->user->notify(new \App\Notifications\LetterStatusUpdated($letterRequest));
            return redirect()->route('kaprodi.request.surat-masuk.index')->with('success', 'Surat telah ditolak.');
        } catch (\Exception $e) {
            Log::error('Error rejecting letter request: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }
}