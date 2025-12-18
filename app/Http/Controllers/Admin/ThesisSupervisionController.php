<?php

namespace App\Http\Controllers\Admin;

use App\Models\Thesis;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\ThesisSupervision;
use App\Models\SupervisionMeeting;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ThesisSupervisionController extends Controller
{
    public function index(Request $request)
    {
        $query = ThesisSupervision::with([
            'student.department',
            'supervisor',
            'thesis',
            'thesis.supervisions.supervisor'
        ])
            ->where('supervisor_role', 'pembimbing_1');  // Only get primary supervisors to avoid duplicates

        // Apply filters
        if ($request->filled('department')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('nama_mhs', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $supervisions = $query->latest()->paginate(10);

        // Get data for dropdowns
        $departments = Department::orderBy('nama')->get();

        // Get available students
        $availableStudents = Student::whereDoesntHave('thesisSupervision')
            ->where('total_sks', '>=', 120)
            ->whereHas('department')
            ->orderBy('nama_mhs')
            ->get();

        // Get all lecturers
        $lecturers = Lecturer::orderBy('nama_dosen')->get();

        // Calculate supervision statistics
        $statistics = [
            'total' => ThesisSupervision::where('supervisor_role', 'pembimbing_1')->count(),
            'active' => ThesisSupervision::where('supervisor_role', 'pembimbing_1')
                ->where('status', 'active')->count(),
            'completed' => ThesisSupervision::where('supervisor_role', 'pembimbing_1')
                ->where('status', 'completed')->count()
        ];

        return view('admin.student.thesis.supervision.index', compact(
            'supervisions',
            'departments',
            'availableStudents',
            'lecturers',
            'statistics'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => [
                'required',
                'exists:students,id',
            ],
            'supervisor1_id' => [
                'required',
                'exists:lecturers,id',
                'different:supervisor2_id'
            ],
            'supervisor2_id' => [
                'required',
                'exists:lecturers,id',
                'different:supervisor1_id'
            ],
        ], [
            'supervisor1_id.different' => 'Pembimbing 1 dan Pembimbing 2 tidak boleh sama.',
            'supervisor2_id.different' => 'Pembimbing 1 dan Pembimbing 2 tidak boleh sama.'
        ]);

        $student = Student::findOrFail($validated['student_id']);
        if ($student->total_sks < 120) {
            return back()->with('error', 'Mahasiswa belum memenuhi syarat minimal 120 SKS untuk penugasan pembimbing.')
                ->withInput();
        }

        try {
            DB::beginTransaction();
            // Check if student already has supervisors
            $existingSupervisors = ThesisSupervision::where('student_id', $validated['student_id'])->count();
            if ($existingSupervisors > 0) {
                return back()->with('error', 'Mahasiswa ini sudah memiliki pembimbing skripsi.')
                    ->withInput();
            }

            // Create thesis record
            $thesis = Thesis::create([
                'student_id' => $validated['student_id'],
                'status' => 'active',
                'start_date' => now()
            ]);
            // Create supervision records for both supervisors
            $supervision1 = ThesisSupervision::create([
                'student_id' => $validated['student_id'],
                'thesis_id' => $thesis->id,
                'supervisor_id' => $validated['supervisor1_id'],
                'supervisor_role' => 'pembimbing_1',
                'status' => 'active',
                'assigned_at' => now(),
            ]);
            $supervision2 = ThesisSupervision::create([
                'student_id' => $validated['student_id'],
                'thesis_id' => $thesis->id,
                'supervisor_id' => $validated['supervisor2_id'],
                'supervisor_role' => 'pembimbing_2',
                'status' => 'active',
                'assigned_at' => now(),
            ]);
            DB::commit();
            return redirect()
                ->route('admin.thesis.supervision.index')
                ->with('success', 'Pembimbing skripsi berhasil ditugaskan.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Thesis Supervision Assignment Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return back()
                ->with('error', 'Terjadi kesalahan saat menugaskan pembimbing skripsi: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(ThesisSupervision $supervision)
    {
        $supervision->load([
            'student.department',
            'primarySupervisor',
            'secondarySupervisor',
            'meetings'
        ]);

        return view('admin.student.thesis.supervision.show', compact('supervision'));
    }

    public function edit($id)
    {
        // Get the thesis supervision with all related data
        $supervision = ThesisSupervision::with([
            'student.department',
            'thesis.supervisions.supervisor'
        ])
            ->where('supervisor_role', 'pembimbing_1')
            ->findOrFail($id);

        // Get the secondary supervision
        $secondarySupervisor = ThesisSupervision::where('thesis_id', $supervision->thesis_id)
            ->where('supervisor_role', 'pembimbing_2')
            ->first();

        // Get all lecturers for dropdowns
        $lecturers = Lecturer::orderBy('nama_dosen')->get();

        return view('admin.student.thesis.supervision.edit', compact(
            'supervision',
            'secondarySupervisor',
            'lecturers'
        ));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'supervisor1_id' => [
                'required',
                'exists:lecturers,id',
                'different:supervisor2_id'
            ],
            'supervisor2_id' => [
                'required',
                'exists:lecturers,id',
                'different:supervisor1_id'
            ],
            'status' => [
                'required',
                'in:active,completed,terminated'
            ]
        ], [
            'supervisor1_id.different' => 'Pembimbing 1 dan Pembimbing 2 tidak boleh sama.',
            'supervisor2_id.different' => 'Pembimbing 1 dan Pembimbing 2 tidak boleh sama.'
        ]);

        try {
            DB::beginTransaction();

            // Get primary supervision record
            $supervision = ThesisSupervision::where('supervisor_role', 'pembimbing_1')
                ->findOrFail($id);

            // Get secondary supervision record
            $secondarySupervisor = ThesisSupervision::where('thesis_id', $supervision->thesis_id)
                ->where('supervisor_role', 'pembimbing_2')
                ->firstOrFail();

            // Update primary supervisor
            $supervision->update([
                'supervisor_id' => $validated['supervisor1_id'],
                'status' => $validated['status']
            ]);

            // Update secondary supervisor
            $secondarySupervisor->update([
                'supervisor_id' => $validated['supervisor2_id'],
                'status' => $validated['status']
            ]);

            // Update thesis status if needed
            if ($validated['status'] !== 'active') {
                $supervision->thesis->update(['status' => $validated['status']]);
            }

            DB::commit();

            return redirect()
                ->route('admin.thesis.supervision.index')
                ->with('success', 'Data bimbingan skripsi berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Thesis Supervision Update Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return back()
                ->with('error', 'Terjadi kesalahan saat memperbarui data bimbingan: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Get primary supervision record
            $supervision = ThesisSupervision::where('supervisor_role', 'pembimbing_1')
                ->findOrFail($id);

            // Get thesis ID before deletion for finding secondary supervision
            $thesisId = $supervision->thesis_id;

            // Get secondary supervision record
            $secondarySupervisor = ThesisSupervision::where('thesis_id', $thesisId)
                ->where('supervisor_role', 'pembimbing_2')
                ->first();

            // Update and delete both supervision records
            if ($secondarySupervisor) {
                $secondarySupervisor->update(['status' => 'terminated']);
                $secondarySupervisor->delete();
            }

            // Update and delete primary supervision
            $supervision->update(['status' => 'terminated']);
            $supervision->delete();

            // Update the thesis status
            $thesis = Thesis::find($thesisId);
            if ($thesis) {
                $thesis->update(['status' => 'terminated']);
            }

            DB::commit();

            return redirect()
                ->route('admin.thesis.supervision.index')
                ->with('success', 'Data bimbingan skripsi berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Thesis Supervision Deletion Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->with('error', 'Terjadi kesalahan saat menghapus data bimbingan: ' . $e->getMessage());
        }
    }
}