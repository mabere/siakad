<?php

namespace App\Http\Controllers\Student;

use App\Models\Thesis;
use Illuminate\Http\Request;
use App\Models\ThesisSupervision;
use App\Models\SupervisionMeeting;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ThesisSupervisionController extends Controller
{
    public function index()
    {
        // Get the authenticated user's student record
        $student = auth()->user()->student;

        if (!$student) {
            return view('mhs.skripsi.index', [
                'supervision' => null,
                'meetings' => [],
                'error' => 'Data mahasiswa tidak ditemukan.'
            ]);
        }

        // Get thesis using student ID
        $thesis = Thesis::where('student_id', $student->id)->first();

        if ($thesis) {
            $supervision = ThesisSupervision::with([
                'thesis',
                'supervisor',
                'thesis.supervisions.supervisor'
            ])
                ->where('thesis_id', $thesis->id)
                ->where('supervisor_role', 'pembimbing_1')
                ->first();
        } else {
            $supervision = null;
        }


        // Initialize empty meetings array
        $meetings = [];

        // Only try to get meetings if the relationship exists and supervision exists
        if ($supervision && method_exists($supervision->thesis, 'meetings')) {
            try {
                $meetings = $supervision->thesis->meetings()
                    ->with('supervisor')
                    ->latest()
                    ->get();
            } catch (\Exception $e) {
                \Log::warning('Error loading meetings:', [
                    'error' => $e->getMessage()
                ]);
                // Meetings will remain an empty array
            }
        }

        return view('mhs.skripsi.index', compact('supervision', 'meetings'));
    }

    public function show($id)
    {
        // Get the authenticated user's student record
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()
                ->route('student.thesis.supervision.index')
                ->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        // Get supervision with relationships and verify ownership
        $supervision = ThesisSupervision::with([
            'thesis',
            'supervisor',
            'thesis.supervisions.supervisor'
        ])
            ->whereHas('thesis', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->where('supervisor_role', 'pembimbing_1')
            ->findOrFail($id);

        // Get meetings if they exist
        $meetings = [];
        if (method_exists($supervision->thesis, 'meetings')) {
            try {
                $meetings = $supervision->thesis->meetings()
                    ->with('supervisor')
                    ->latest()
                    ->get();
            } catch (\Exception $e) {
                \Log::warning('Error loading meetings:', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return view('mhs.skripsi.show', compact('supervision', 'meetings'));
    }


    public function create($supervisorRole)
    {
        // Get student's thesis supervision
        $student = auth()->user()->student;
        $thesis = $student->thesis;

        if (!$thesis) {
            return redirect()
                ->route('mhs.skripsi.index')
                ->with('error', 'Data skripsi tidak ditemukan.');
        }

        // Get the correct supervisor based on role
        $supervision = $thesis->supervisions()
            ->where('supervisor_role', $supervisorRole)
            ->first();

        if (!$supervision) {
            return redirect()
                ->route('mhs.skripsi.index')
                ->with('error', 'Data pembimbing tidak ditemukan.');
        }

        return view('mhs.skripsi.meetings.create', compact('supervision', 'supervisorRole'));
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

            // Handle file upload if present
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('supervision-attachments', 'public');
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
            \Log::error('Error creating supervision meeting:', [
                'error' => $e->getMessage()
            ]);

            return back()
                ->with('error', 'Terjadi kesalahan saat mengajukan bimbingan.')
                ->withInput();
        }
    }

    public function showBimbingan(SupervisionMeeting $meeting)
    {
        // Verify that the logged-in student owns this meeting
        $student = auth()->user()->student;

        if ($meeting->thesis->student_id !== $student->id) {
            return redirect()
                ->route('student.thesis.supervision.index')
                ->with('error', 'Anda tidak memiliki akses ke data bimbingan ini.');
        }

        // Load necessary relationships
        $meeting->load(['supervisor', 'thesis']);

        return view('mhs.skripsi.meetings.show', compact('meeting'));
    }


    // app/Http/Controllers/Student/ThesisMeetingHistoryController.php

    public function printHistory()
    {
        $student = auth()->user()->student;
        $thesis = $student->thesis;

        // Dapatkan ID supervisor dari ThesisSupervision
        $supervisor1Id = ThesisSupervision::where('thesis_id', $thesis->id)
            ->where('supervisor_role', 'pembimbing_1')
            ->value('supervisor_id');

        $supervisor2Id = ThesisSupervision::where('thesis_id', $thesis->id)
            ->where('supervisor_role', 'pembimbing_2')
            ->value('supervisor_id');

        // Ambil meetings berdasarkan supervisor_id
        $supervisor1Meetings = SupervisionMeeting::with(['supervisor'])
            ->where('thesis_id', $thesis->id)
            ->where('supervisor_id', $supervisor1Id)
            ->orderBy('meeting_date', 'asc')
            ->get();

        $supervisor2Meetings = SupervisionMeeting::with(['supervisor'])
            ->where('thesis_id', $thesis->id)
            ->where('supervisor_id', $supervisor2Id)
            ->orderBy('meeting_date', 'asc')
            ->get();
        $yay = "YAYASA LAKIDENDE RAZAK POROZI";
        $univ = "UNIVERSITAS LAKIDENDE UNAAHA";
        return view('mhs.skripsi.meetings.print-history', compact(
            'student',
            'thesis',
            'supervisor1Meetings',
            'supervisor2Meetings',
            'yay',
            'univ'
        ));
    }

}