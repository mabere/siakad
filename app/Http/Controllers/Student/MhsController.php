<?php

namespace App\Http\Controllers\Student;

use DateTime;
use Carbon\Carbon;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Response;
use App\Models\Schedule;
use App\Models\StudyPlan;
use App\Models\Attendance;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\Questionnaire;
use App\Models\AttendanceDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MhsController extends Controller
{
    // Halaman Dashboard Mahasiswa
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('mahasiswa')) {
            return redirect()->route('login')
                ->with('error', 'Silakan login sebagai mahasiswa');
        }

        // Load relasi student jika belum
        if (!$user->relationLoaded('student')) {
            $user->load('student');
        }
        $student = $user->student;
        if (!$student) {
            return redirect()->route('login')
                ->with('error', 'Data mahasiswa tidak ditemukan');
        }

        $ta = getCurrentAcademicYear();

        // Hitung IPK
        $ipk = Grade::calculateIpk($student->id, $ta->id);

        // Tentukan level semester mahasiswa
        $currentSemester = $student->getCurrentSemester($ta->ta, $ta->semester);

        // Simpan atau perbarui status semester di student_semester_status
        $status = $student->statusSemesters()
            ->where('academic_year_id', $ta->id)
            ->where('semester', $currentSemester)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$status) {
            $status = $student->statusSemesters()->create([
                'academic_year_id' => $ta->id,
                'semester' => $currentSemester,
                'status' => 'aktif',
                'effective_date' => now(),
            ]);
        }

        $statusText = $status->status;

        $studyPlans = StudyPlan::where('student_id', $student->id)
            ->where('academic_year_id', $ta->id)
            ->get();

        // Create grades and attendances for each study plan if not exists
        foreach ($studyPlans as $studyPlan) {
            $gradeExists = Grade::where('study_plan_id', $studyPlan->id)->exists();
            if (!$gradeExists) {
                Grade::create([
                    'study_plan_id' => $studyPlan->id,
                    'student_id' => $student->id,
                    'schedule_id' => $studyPlan->schedule_id,
                    'academic_year_id' => $studyPlan->academic_year_id
                ]);
            }
            $attendanceExists = Attendance::where('study_plan_id', $studyPlan->id)->exists();
            if (!$attendanceExists) {
                Attendance::create([
                    'study_plan_id' => $studyPlan->id,
                    'student_id' => $student->id,
                    'schedule_id' => $studyPlan->schedule_id,
                    'academic_year_id' => $studyPlan->academic_year_id
                ]);
            }
        }

        $questionnaires = Questionnaire::with('questions')
            ->where('is_active', true)
            ->get();

        // Ambil riwayat IPK per tahun akademik
        $ipkHistory = Grade::getIpkHistory($student->id);

        $labels = $ipkHistory->pluck('year')->toArray();
        $data = $ipkHistory->pluck('ipk')->toArray();

        // Validasi EDOM untuk dashboard
        $grades = Grade::where('student_id', $student->id)
            ->where('academic_year_id', $ta->id)
            ->where('validation_status', 'locked')
            ->with('schedule')
            ->get();

        $incompleteEvaluations = [];
        foreach ($grades as $grade) {
            $response = Response::where('student_id', $student->id)
                ->where('schedule_id', $grade->schedule_id)
                ->whereHas('questionnaire', function ($query) {
                    $query->active();
                })
                ->exists();
            if (!$response) {
                $incompleteEvaluations[] = $grade->schedule_id;
            }
        }

        $incompleteEdomCount = count($incompleteEvaluations);
        $hasIncompleteEdom = $incompleteEdomCount > 0;

        return view('mhs.dashboard', [
            'mahasiswa' => $user,
            'student' => $student,
            'questionnaires' => $questionnaires,
            'studyPlans' => $studyPlans,
            'ipk' => $ipk,
            'currentSemester' => $currentSemester,
            'status' => $statusText,
            'ipkLabels' => $labels,
            'ipkData' => $data,
            'hasIncompleteEdom' => $hasIncompleteEdom,
            'incompleteEdomCount' => $incompleteEdomCount,
        ]);
    }

    public function printKtm()
    {
        $students = Student::with(['department'])->get();
        return view('mhs.ktm', compact('students'));
    }

    public function showScanQR()
    {
        return view('mhs.presensi.scan-qr');
    }

    public function verifyAttendanceQR(Request $request)
    {
        try {
            $data = json_decode($request->input('qr_data'), true);

            if (!$data || !isset($data['schedule_id'], $data['meeting'], $data['timestamp'], $data['lecturer_id'])) {
                return response()->json(['error' => 'Data QR tidak valid'], 400);
            }

            $scheduleId = $data['schedule_id'];
            $meeting = $data['meeting'];
            $lecturerId = $data['lecturer_id'];
            $timestamp = $data['timestamp'];

            // Validasi waktu (15 menit)
            $scanTime = now();
            $qrTime = new DateTime($timestamp);
            if ($scanTime->diff($qrTime)->i > 15) {
                return response()->json(['error' => 'QR code telah kedaluwarsa'], 400);
            }

            $jadwal = Schedule::where('id', $scheduleId)
                ->whereHas('lecturersInSchedule', function ($query) use ($lecturerId) {
                    $query->where('lecturer_id', $lecturerId);
                })
                ->firstOrFail();

            $student = auth()->user()->student; // Asumsi mahasiswa sudah login
            if (!$student) {
                return response()->json(['error' => 'Autentikasi mahasiswa gagal'], 401);
            }

            $attendance = Attendance::firstOrCreate([
                'schedule_id' => $scheduleId,
                'student_id' => $student->id,
                'academic_year_id' => $this->currentAcademicYear->id,
                'study_plan_id' => StudyPlan::where('schedule_id', $scheduleId)
                    ->where('student_id', $student->id)
                    ->where('status', 'approved')
                    ->first()->id ?? null
            ]);

            AttendanceDetail::updateOrCreate(
                ['attendance_id' => $attendance->id, 'meeting_number' => $meeting],
                ['status' => 'Hadir', 'updated_at' => now()]
            );

            return response()->json(['success' => 'Kehadiran berhasil dicatat'], 200);

        } catch (\Exception $e) {
            \Log::error('Error verifying QR attendance: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
