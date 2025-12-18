<?php

namespace App\Http\Controllers\Lecturer;

use Illuminate\Http\Request;
use App\Models\SupervisionMeeting;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class SupervisionMeetingController extends Controller
{
    public function create($supervisorRole)
    {
        // Get student's thesis supervision
        $student = auth()->user()->student;
        $thesis = $student->thesis;

        if (!$thesis) {
            return redirect()
                ->route('student.thesis.supervision.index')
                ->with('error', 'Data skripsi tidak ditemukan.');
        }

        // Get the correct supervisor based on role
        $supervision = $thesis->supervisions()
            ->where('supervisor_role', $supervisorRole)
            ->first();

        if (!$supervision) {
            return redirect()
                ->route('student.thesis.supervision.index')
                ->with('error', 'Data pembimbing tidak ditemukan.');
        }

        // Check for pending meetings
        $hasPendingMeeting = SupervisionMeeting::hasPendingMeetings(
            $thesis->id,
            $supervision->supervisor_id
        );

        if ($hasPendingMeeting) {
            return redirect()
                ->route('student.thesis.supervision.index')
                ->with('error', 'Anda masih memiliki pengajuan bimbingan yang belum direspon. Silakan tunggu respon dari dosen pembimbing.');
        }

        return view('student.thesis.supervision.meetings.create', compact('supervision', 'supervisorRole'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supervisor_id' => 'required|exists:lecturers,id',
            'meeting_date' => 'required|date|after:today',
            'topic' => 'required|string|max:255',
            'description' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:2048'
        ]);

        try {
            $student = auth()->user()->student;
            $thesis = $student->thesis;

            // Check for pending meetings again (double validation)
            if (SupervisionMeeting::hasPendingMeetings($thesis->id, $validated['supervisor_id'])) {
                return back()
                    ->with('error', 'Anda masih memiliki pengajuan bimbingan yang belum direspon.')
                    ->withInput();
            }

            // Handle file upload if present
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('supervision-attachments');
            }

            // Create meeting request
            $meeting = SupervisionMeeting::create([
                'thesis_id' => $thesis->id,
                'supervisor_id' => $validated['supervisor_id'],
                'meeting_date' => $validated['meeting_date'],
                'topic' => $validated['topic'],
                'description' => $validated['description'],
                'attachment_path' => $attachmentPath,
                'status' => 'pending'
            ]);

            return redirect()
                ->route('student.thesis.supervision.index')
                ->with('success', 'Permintaan bimbingan berhasil diajukan.');

        } catch (\Exception $e) {
            Log::error('Error creating supervision meeting:', [
                'error' => $e->getMessage()
            ]);

            return back()
                ->with('error', 'Terjadi kesalahan saat mengajukan bimbingan.')
                ->withInput();
        }
    }
}