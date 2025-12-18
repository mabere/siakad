<?php

namespace App\Http\Controllers\Surat;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\LetterRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\LetterRequest\FacultyLetterRequestService;

class LetterRequestByDekanController extends Controller
{
    protected $service;

    public function __construct(FacultyLetterRequestService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $facultyId = Auth::user()->lecturer->department->faculty_id;

        $letterRequests = LetterRequest::whereHas('letterType', function ($query) use ($facultyId) {
            $query->whereExists(function ($subQuery) use ($facultyId) {
                $subQuery->select(DB::raw(1))
                    ->from('letter_type_assignments')
                    ->whereColumn('letter_type_assignments.letter_type_id', 'letter_types.id')
                    ->where('letter_type_assignments.faculty_id', $facultyId);
            });
        })->with(['letterType', 'user'])
            ->paginate(10);

        return view('letter-request.dekan.index', compact('letterRequests'));
    }

    public function show(LetterRequest $letterRequest)
    {
        $this->authorize('view', $letterRequest);
        $letterRequest->load('user', 'user.student.department', 'user.lecturer.department', 'letterType');

        // Tambah data nama pengguna ke approval_history
        $historyWithNames = collect($letterRequest->approval_history)->map(function ($history) {
            $user = User::find($history['user_id']);
            $history['user_name'] = $user ? $user->name : 'User Tidak Ditemukan';
            return $history;
        });
        $letterRequest->approval_history = $historyWithNames->toArray();

        return view('letter-request.dekan.show', compact('letterRequest'));
    }

    public function download(LetterRequest $letterRequest)
    {
        try {
            return $this->service->download($letterRequest);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    // Pembaruan kolom approval_flow
    public function approve(Request $request, LetterRequest $letterRequest)
    {
        $this->authorize('approve', $letterRequest);

        $request->validate([
            'notes' => 'nullable|string|max:255',
        ]);
        try {
            $this->service->approve($letterRequest, Auth::user(), $request->notes);
            $letterRequest->user->notify(new \App\Notifications\LetterStatusUpdated($letterRequest));
            return redirect()->route('dekan.request.surat-masuk.index')->with('success', 'Surat telah disetujui.');
        } catch (\Exception $e) {
            Log::error('Error approving letter request: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, LetterRequest $letterRequest)
    {
        $this->authorize('approve', $letterRequest);

        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        try {
            $this->service->reject($letterRequest, Auth::user(), $request->rejection_reason);
            $letterRequest->user->notify(new \App\Notifications\LetterStatusUpdated($letterRequest));
            return redirect()->route('dekan.request.surat-masuk.index')->with('success', 'Surat telah ditolak.');
        } catch (\Exception $e) {
            Log::error('Error rejecting letter request: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

}