<?php

namespace App\Http\Controllers\Student;

use DateTime;
use Carbon\Carbon;
use App\Models\Grade;
use App\Models\Course;
use App\Models\Setting;
use App\Models\Response;
use App\Models\Attendance;
use App\Models\MkduCourse;
use App\Models\EdomSetting;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\AttendanceDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NilaiMhsController extends Controller
{
    public function indexs(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('mahasiswa')) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai mahasiswa.');
        }

        $mahasiswa = $user->student;
        if (!$mahasiswa) {
            return redirect()->route('login')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $ta = getCurrentAcademicYear();
        if (!$ta) {
            return back()->with('error', 'Tidak ada tahun akademik yang aktif.');
        }

        if (!Setting::isEdomActive()) {
            return back()->with('error', 'Evaluasi Dosen oleh Mahasiswa tidak aktif.');
        }
        // Menggunakan eager loading polimorfik untuk schedulable dan responses
        $grades = Grade::where('student_id', $mahasiswa->id)
            ->where('academic_year_id', $ta->id)
            ->where('validation_status', 'locked')
            ->with([
                'schedule.kelas',
                'schedule.lecturersInSchedule',
                'schedule.schedulable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Course::class => [],
                        MkduCourse::class => [],
                    ]);
                },
                // Eager load responses untuk menghindari N+1 query
                'schedule.responses' => fn($query) => $query->where('student_id', $mahasiswa->id)
            ])
            ->get();

        // Validasi EDOM: Gunakan collection method untuk menghindari loop dan query tambahan
        $incompleteEvaluations = $grades->filter(function ($grade) {
            return $grade->schedule->responses->isEmpty();
        })->map(function ($grade) {
            return $grade->schedule->schedulable->name;
        })->values();


        if ($incompleteEvaluations->isNotEmpty()) {
            return redirect()->route('student.edom.index')
                ->with('error', 'Silakan mengisi evaluasi dosen dulu untuk mata kuliah berikut: ' . $incompleteEvaluations->implode(', ') . ' sebelum bisa melihat nilai.');
        }

        // Hitung total SKS dan bobot dalam satu loop yang efisien
        $totalSks = 0;
        $totalBobot = 0;
        foreach ($grades as $grade) {
            $sks = $grade->schedule->schedulable->sks ?? 0;
            $angka = match ($grade->nhuruf) {
                'A' => 4,
                'B' => 3,
                'C' => 2,
                'D' => 1,
                default => 0,
            };
            $bobot = $angka * $sks;
            $totalSks += $sks;
            $totalBobot += $bobot;
        }

        return view('mhs.nilai.index')->with([
            'ta' => $ta,
            'grades' => $grades,
            'totalSks' => $totalSks,
            'totalBobot' => $totalBobot,
            'mahasiswa' => $mahasiswa,
        ]);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('mahasiswa')) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai mahasiswa.');
        }

        $mahasiswa = $user->student;
        if (!$mahasiswa) {
            return redirect()->route('login')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $ta = getCurrentAcademicYear();
        if (!$ta) {
            return back()->with('error', 'Tidak ada tahun akademik yang aktif.');
        }

        if (!Setting::isEdomActive()) {
            return back()->with('error', 'Evaluasi Dosen oleh Mahasiswa tidak aktif.');
        }

        // Ambil SEMUA grade untuk mahasiswa dan tahun akademik saat ini
        $grades = Grade::where('student_id', $mahasiswa->id)
            ->where('academic_year_id', $ta->id)
            ->with([
                'schedule.kelas',
                'schedule.lecturersInSchedule',
                'schedule.schedulable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Course::class => [],
                        MkduCourse::class => [],
                    ]);
                },
                'schedule.responses' => fn($query) => $query->where('student_id', $mahasiswa->id)
            ])
            ->get();

        // Validasi EDOM: Gunakan collection method
        $incompleteEvaluations = $grades->filter(function ($grade) {
            return $grade->schedule->responses->isEmpty();
        })->map(function ($grade) {
            return $grade->schedule->schedulable->name;
        })->values();

        if ($incompleteEvaluations->isNotEmpty()) {
            return redirect()->route('student.edom.index')
                ->with('error', 'Silakan mengisi evaluasi dosen dulu untuk mata kuliah berikut: ' . $incompleteEvaluations->implode(', ') . ' sebelum bisa melihat nilai.');
        }

        // Tentukan apakah tombol cetak bisa ditampilkan. True jika SEMUA grade locked.
        $canPrintKHS = $grades->isNotEmpty() && $grades->every(fn($grade) => $grade->validation_status === 'locked');

        // Hitung total SKS dan bobot HANYA untuk grade yang berstatus 'locked'
        $lockedGrades = $grades->filter(fn($grade) => $grade->validation_status === 'locked');

        $totalSks = 0;
        $totalBobot = 0;

        foreach ($lockedGrades as $grade) {
            $sks = $grade->schedule->schedulable->sks ?? 0;
            $angka = match ($grade->nhuruf) {
                'A' => 4,
                'B' => 3,
                'C' => 2,
                'D' => 1,
                default => 0,
            };
            $bobot = $angka * $sks;
            $totalSks += $sks;
            $totalBobot += $bobot;
        }

        return view('mhs.nilai.index')->with([
            'ta' => $ta,
            'grades' => $grades,
            'totalSks' => $totalSks,
            'totalBobot' => $totalBobot,
            'mahasiswa' => $mahasiswa,
            'canPrintKHS' => $canPrintKHS,
        ]);
    }

    public function print(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('mahasiswa')) {
            return redirect()->route('login')
                ->with('error', 'Silakan login sebagai mahasiswa.');
        }

        $mahasiswa = $user->student;

        if (!$mahasiswa || $mahasiswa->id != $id) {
            return redirect()->route('student.nilai.index')
                ->with('error', 'Data mahasiswa tidak valid.');
        }

        // Ambil tahun akademik yang aktif
        $ta = getCurrentAcademicYear();
        if (!$ta) {
            return back()->with('error', 'Tidak ada tahun akademik yang aktif.');
        }

        // Ambil data nilai mahasiswa dengan relasi schedule
        $grades = Grade::where('student_id', $mahasiswa->id)
            ->where('academic_year_id', $ta->id)
            ->where('validation_status', 'locked') // Hanya tampilkan nilai yang terkunci
            ->with(['schedule.course', 'schedule.kelas']) // Muat relasi schedule
            ->get();

        $totalSks = 0;
        $totalBobot = 0;
        foreach ($grades as $index => $nilai) {
            // Jumlahkan SKS
            $totalSks += $nilai->schedule->course->sks ?? 0;
            // Hitung bobot
            $angka = match ($nilai->nhuruf) {
                'A' => 4,
                'B' => 3,
                'C' => 2,
                'D' => 1,
                default => 0,
            };
            $bobot = $angka * ($nilai->schedule->course->sks ?? 0);
            $totalBobot += $bobot;
        }

        // Ambil tanggal sekarang
        Carbon::setLocale('id');
        $dateNow = Carbon::now()->translatedFormat('d F Y');
        $tgl = $dateNow;

        return view('mhs.nilai.print')->with([
            'grades' => $grades,
            'mahasiswa' => $mahasiswa,
            'ta' => $ta,
            'tgl' => $tgl,
            'totalSks' => $totalSks,
            'totalBobot' => $totalBobot,
        ]);
    }

    public function presensi(Request $request)
    {
        try {
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

            // Ambil tahun akademik aktif
            $ta = getCurrentAcademicYear();
            if (!$ta) {
                return back()->with('error', 'Tidak ada tahun akademik yang aktif');
            }

            // Ambil data presensi berdasarkan attendance_details
            $attendances = Attendance::with(['schedule.course', 'schedule.lecturersInSchedule', 'attendanceDetails'])
                ->where('student_id', $student->id)
                ->where('academic_year_id', $ta->id)
                ->get();

            // Tentukan maxMeeting berdasarkan jadwal secara keseluruhan
            $maxMeetingGlobal = AttendanceDetail::join('attendances', 'attendance_details.attendance_id', '=', 'attendances.id')
                ->whereIn('attendances.schedule_id', $attendances->pluck('schedule_id'))
                ->max('meeting_number') ?: 0;

            $totalMeetings = min(16, max(1, $maxMeetingGlobal)); // Maksimum 16, minimal 1

            // Hitung total presensi per jadwal
            $presensi = $attendances->map(function ($attendance) use ($totalMeetings) {
                $details = $attendance->attendanceDetails;
                $actualMeetings = $details->count();

                $statusCounts = [
                    'total_hadir' => $details->where('status', 'Hadir')->count(),
                    'total_izin' => $details->where('status', 'Izin')->count(),
                    'sakit' => $details->where('status', 'Sakit')->count(),
                    'total_alfa' => 0,
                ];

                // Hitung alfa hanya untuk pertemuan yang sudah dilaksanakan
                if ($totalMeetings > 0) {
                    $filledMeetings = $details->count();
                    $unaccounted = min($totalMeetings, $filledMeetings); // Jangan melebihi total yang dilaksanakan
                    $statusCounts['total_alfa'] = max(0, $totalMeetings - $filledMeetings) + $details->whereIn('status', ['Alpha', null, 'Tanpa Keterangan'])->count();
                }

                return [
                    'schedule' => $attendance->schedule,
                    'total_hadir' => $statusCounts['total_hadir'],
                    'total_izin' => $statusCounts['total_izin'],
                    'sakit' => $statusCounts['sakit'],
                    'total_alfa' => $statusCounts['total_alfa'],
                    'current_meeting' => $totalMeetings, // Tambahkan current meeting
                ];
            });
            return view('mhs.presensi.index', [
                'ta' => $ta,
                'presensi' => $presensi,
                'mahasiswa' => $user,
                'student' => $student
            ]);

        } catch (\Exception $e) {
            \Log::error('Presensi Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

}
