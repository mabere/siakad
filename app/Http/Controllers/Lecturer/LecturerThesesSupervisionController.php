<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ThesisSupervision;
use App\Models\SupervisionMeeting;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LecturerThesesSupervisionController extends Controller
{
    public function index()
    {
        $lecturerId = auth()->user()->lecturer->id;

        $supervisions = $this->getSupervisions($lecturerId);
        $pendingMeetings = $this->getMeetings($lecturerId, 'pending', 'asc');
        $recentMeetings = $this->getMeetings($lecturerId, ['approved', 'rejected'], 'desc', 5);

        return view('dosen.bimbingan.index', compact(
            'supervisions',
            'pendingMeetings',
            'recentMeetings'
        ));
    }

    public function show($id)
    {
        $lecturerId = auth()->user()->lecturer->id;

        $supervision = $this->getSupervisionById($id, $lecturerId);
        $meetings = $this->getMeetingsByThesisId($supervision->thesis_id);

        $overallProgress = $this->calculateOverallProgress($supervision);

        return view('dosen.bimbingan.show', compact('supervision', 'meetings', 'overallProgress'));
    }

    public function respondToMeeting(Request $request, SupervisionMeeting $meeting)
    {
        if (!$this->isMeetingOwnedByLecturer($meeting)) {
            return $this->redirectBackWithError('Anda tidak memiliki akses untuk menanggapi bimbingan ini.');
        }

        if ($meeting->status !== 'pending') {
            return $this->redirectBackWithError('Bimbingan ini sudah ditanggapi sebelumnya.');
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'required|string|max:1000'
        ]);

        return $this->updateMeetingStatus($meeting, $validated);
    }

    private function getSupervisions($lecturerId)
    {
        return ThesisSupervision::with(['thesis.student', 'supervisor'])
            ->where('supervisor_id', $lecturerId)
            ->get()
            ->groupBy('thesis_id');
    }

    private function getMeetings($lecturerId, $status, $order, $limit = null)
    {
        $query = SupervisionMeeting::with(['thesis.student', 'supervisor'])
            ->where('supervisor_id', $lecturerId)
            ->whereIn('status', (array) $status)
            ->orderBy('meeting_date', $order);

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    private function getSupervisionById($id, $lecturerId)
    {
        return ThesisSupervision::with([
            'thesis.student',
            'supervisor',
            'thesis.supervisions.supervisor',
            'thesis.milestones.tasks'
        ])
            ->where('supervisor_id', $lecturerId)
            ->findOrFail($id);
    }

    private function getMeetingsByThesisId($thesisId)
    {
        $lecturerId = auth()->user()->lecturer->id;
        return SupervisionMeeting::with(['supervisor'])
            ->where('thesis_id', $thesisId)
            ->where('supervisor_id', $lecturerId)
            ->orderBy('meeting_date', 'desc')
            ->get();
    }

    private function calculateOverallProgress($supervision)
    {
        $totalMilestones = $supervision->thesis->milestones->count();
        $completedMilestones = $supervision->thesis->milestones->where('status', 'completed')->count();
        return $totalMilestones > 0 ? round(($completedMilestones / $totalMilestones) * 100) : 0;
    }

    private function isMeetingOwnedByLecturer($meeting)
    {
        return $meeting->supervisor_id === auth()->user()->lecturer->id;
    }

    private function redirectBackWithError($message)
    {
        return redirect()->back()->with('error', $message);
    }

    private function updateMeetingStatus($meeting, $validated)
    {
        try {
            DB::beginTransaction();

            $updated = $meeting->update([
                'status' => $validated['status'],
                'notes' => trim($validated['notes']),
                'response_date' => now()
            ]);
            DB::commit();

            session()->flash('success', 'Tanggapan bimbingan berhasil disimpan.');

            return redirect()
                ->route('lecturer.thesis.supervision.show', $meeting->thesis_supervision_id)
                ->with('success', 'Tanggapan bimbingan berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error in respondToMeeting:', [
                'meeting_id' => $meeting->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan tanggapan: ' . $e->getMessage())
                ->withInput();
        }
    }
}
