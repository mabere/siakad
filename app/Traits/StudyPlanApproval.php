<?php

namespace App\Traits;

use App\Models\StudyPlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait StudyPlanApproval
{
    public function approveKRS($studyPlanId, $notes = null)
    {
        $studyPlan = StudyPlan::findOrFail($studyPlanId);
        
        return $studyPlan->update([
            'status' => 'approved',
            'approved_by' => request()->session()->get('dosen')->id,
            'approved_at' => now(),
            'notes' => $notes
        ]);
    }

    public function rejectKRS($studyPlanId, $notes)
    {
        $studyPlan = StudyPlan::findOrFail($studyPlanId);
        
        return $studyPlan->update([
            'status' => 'rejected',
            'approved_by' => request()->session()->get('dosen')->id,
            'approved_at' => now(),
            'notes' => $notes
        ]);
    }

    public function getPendingKRS($advisorId)
    {
        // Get student IDs from dosen's class
        $studentIds = DB::table('students')
            ->join('kelas', 'students.kelas_id', '=', 'kelas.id')
            ->where('kelas.lecturer_id', $advisorId)
            ->pluck('students.id');
            
        return StudyPlan::with(['student.kelas', 'schedule.course', 'schedule.lecturersInSchedule'])
            ->whereIn('student_id', $studentIds)
            ->where('status', 'pending')
            ->get()
            ->groupBy('student_id');
    }

    public function getKRSHistory($studentId)
    {
        return StudyPlan::where('student_id', $studentId)
            ->with(['schedule.course', 'approvedBy'])
            ->latest()
            ->get();
    }
}
