<?php

namespace App\Http\Controllers\Lecturer;

use App\Models\StudyPlan;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Traits\StudyPlanApproval;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class KrsApprovalController extends Controller
{
    use StudyPlanApproval;

    public function index(Request $request)
    {
        $lecturer = auth()->user()->lecturer;
        $activeAcademicYear = AcademicYear::where('status', 1)->first();

        if (!$activeAcademicYear) {
            return view('dosen.approval-krs.index', [
                'pendingKrs' => collect([]),
                'processedKrs' => collect([]),
                'error' => 'Tidak ada tahun akademik yang aktif'
            ]);
        }

        // Get student IDs dari anak bimbingan dosen
        $studentIds = DB::table('students')
            ->where('advisor_id', $lecturer->id)
            ->pluck('students.id');

        // Get pending KRS
        $pendingKrs = StudyPlan::with(['student.kelas', 'schedule.course', 'schedule.lecturersInSchedule'])
            ->whereIn('student_id', $studentIds)
            ->where('status', 'pending')
            ->where('academic_year_id', $activeAcademicYear->id)
            ->get()
            ->groupBy('student_id');

        // Get processed KRS (approved/rejected), grouped by academic year
        $processedKrs = StudyPlan::with(['student.kelas', 'schedule.course', 'schedule.lecturersInSchedule'])
            ->whereIn('student_id', $studentIds)
            ->whereIn('status', ['approved', 'rejected'])
            ->get()
            ->groupBy('academic_year_id');

        return view('dosen.approval-krs.krs-approval', [
            'pendingKrs' => $pendingKrs,
            'processedKrs' => $processedKrs,
            'activeAcademicYear' => $activeAcademicYear
        ]);
    }

    public function showKrs($studentId)
    {
        $krsHistory = $this->getKRSHistory($studentId);
        return view('lecturer.krs.approval.show', compact('krsHistory'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:255'
        ]);

        $this->updateKRSStatus($id, 'approved', $request->notes);
        return back()->with('success', 'KRS berhasil disetujui');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|max:255'
        ]);

        $this->updateKRSStatus($id, 'rejected', $request->notes);
        return back()->with('success', 'KRS berhasil ditolak');
    }

    private function updateKRSStatus($studyPlanId, $status, $notes = null)
    {
        $studyPlan = StudyPlan::findOrFail($studyPlanId);

        return $studyPlan->update([
            'status' => $status,
            'approved_by' => auth()->user()->lecturer->id,
            'approved_at' => now(),
            'notes' => $notes
        ]);
    }

    public function bulkApprove(Request $request, $studentId)
    {
        $request->validate([
            'notes' => 'nullable|string|max:255'
        ]);

        $this->updateBulkKRSStatus($studentId, 'approved', $request->notes);
        return redirect()->route('lecturer.krs.index')->with('success', 'Semua KRS mahasiswa berhasil disetujui');
    }

    public function bulkReject(Request $request, $studentId)
    {
        $request->validate([
            'notes' => 'required|string|max:255'
        ]);

        $this->updateBulkKRSStatus($studentId, 'rejected', $request->notes);
        return redirect()->route('lecturer.krs.index')->with('success', 'Semua KRS mahasiswa berhasil ditolak');
    }


    private function updateBulkKRSStatus($studentId, $status, $notes = null)
    {
        $studyPlans = StudyPlan::where('student_id', $studentId)
            ->where('status', 'pending')
            ->get();

        foreach ($studyPlans as $studyPlan) {
            $studyPlan->update([
                'status' => $status,
                'approved_by' => Auth::user()->lecturer->id,
                'approved_at' => now(),
                'notes' => $notes
            ]);
        }

        return true;
    }


}
