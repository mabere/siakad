<?php
namespace App\Http\Controllers\Surat;

use App\Models\LetterRequest;
use App\Services\LetterRequest\FacultyLetterRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class KaprodiReviewDosenRequestController extends Controller
{
    protected $service;

    public function __construct(FacultyLetterRequestService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $user = Auth::user();
        $facultyId = $user->lecturer->department->faculty_id ?? null;

        $suratMasuk = LetterRequest::whereHas('tipeSurat', function ($query) use ($facultyId) {
            $query->where('faculty_id', $facultyId);
        })
            ->where('status', 'submitted')
            ->with(['tipeSurat', 'user'])
            ->latest()
            ->paginate(10);

        return view('letter-request.kaprodi.dosen.index', compact('suratMasuk'));
    }

    public function review(LetterRequest $letterRequest)
    {
        $this->authorize('review', $letterRequest);

        return view('letter-request.kaprodi.dosen.review', compact('letterRequest'));
    }

    public function approve(Request $request, LetterRequest $letterRequest)
    {
        $this->authorize('review', $letterRequest);

        $letterRequest->approval_flow['review'] = 'approved';
        $letterRequest->status = 'processing';
        $letterRequest->approval_history[] = [
            'step' => 'review',
            'user_id' => Auth::id(),
            'timestamp' => now()->toDateTimeString(),
        ];

        $letterRequest->save();

        $letterRequest->user->notify(new \App\Notifications\LetterStatusUpdated($letterRequest));

        return redirect()->route('kaprodi.request.review.dosen.index')->with('success', 'Surat berhasil direview dan sedang diproses.');
    }
}