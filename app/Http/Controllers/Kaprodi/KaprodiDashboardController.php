<?php

namespace App\Http\Controllers\Kaprodi;

use App\Models\Grade;
use App\Models\Kelas;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Department;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\LetterRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class KaprodiDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user()->load('lecturer.department');

        // Validate user permissions
        if (!$user->hasRole('kaprodi') || !$user->lecturer || !$user->lecturer->department || !$user->lecturer->department->faculty_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses dashboard.');
        }

        $departmentId = $user->lecturer->department->id;
        $facultyId = $user->lecturer->department->faculty_id;
        $department = $user->lecturer->department->nama;
        $ta = getCurrentAcademicYear();

        $stats = Cache::remember("kaprodi_stats_{$departmentId}", 3600, function () use ($departmentId) {
            return [
                'totalMahasiswa' => Student::where('department_id', $departmentId)->count(),
                'totalKelas' => Kelas::where('department_id', $departmentId)->count(),
                'totalDosen' => Lecturer::where('department_id', $departmentId)->count(),
                'mahasiswaTanpaKelas' => Student::where('department_id', $departmentId)
                    ->whereNull('kelas_id')
                    ->count(),

                'totalSuratMasuk' => LetterRequest::whereHas('letterType', function ($q) use ($departmentId) {
                    $q->where('level', 'department')
                        ->whereExists(function ($subQuery) use ($departmentId) {
                            $subQuery->select(DB::raw(1))
                                ->from('letter_type_assignments')
                                ->whereColumn('letter_type_assignments.letter_type_id', 'letter_types.id')
                                ->where('letter_type_assignments.department_id', $departmentId);
                        });
                })->whereHas('user.student', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })->count(),
            ];
        });

        $topIpkStudents = $this->getTopIpkStudentsByDepartment($departmentId, $ta->id);

        $ipkDistribution = $this->getIpkDistribution($departmentId, $ta->id);


        $suratTerbaru = LetterRequest::whereHas('letterType', function ($query) use ($departmentId, $facultyId) {
            $query->whereExists(function ($subQuery) use ($departmentId, $facultyId) {
                $subQuery->select(DB::raw(1))
                    ->from('letter_type_assignments')
                    ->whereColumn('letter_type_assignments.letter_type_id', 'letter_types.id')
                    ->where(function ($q) use ($departmentId, $facultyId) {
                        $q->where('letter_type_assignments.department_id', $departmentId)
                            ->orWhere('letter_type_assignments.faculty_id', $facultyId);
                    });
            });
        })->where(function ($q) use ($departmentId) {
            $q->whereHas('user.student', fn($q) => $q->where('department_id', $departmentId))
                ->orWhereHas('user.lecturer', fn($q) => $q->where('department_id', $departmentId));
        })->with(['user', 'letterType'])
            ->latest()
            ->take(3)
            ->get();

        $kelas = Kelas::where('department_id', $departmentId)
            ->with(['lecturer'])
            ->latest()
            ->take(5)
            ->get();

        $notifikasi = [];
        if ($stats['mahasiswaTanpaKelas'] > 0) {
            $notifikasi[] = "Ada {$stats['mahasiswaTanpaKelas']} mahasiswa belum dialokasikan ke kelas.";
        }

        return view('kaprodi.dashboard', array_merge($stats, compact(
            'topIpkStudents',
            'ipkDistribution',
            'suratTerbaru',
            'kelas',
            'notifikasi',
            'department',
            'ta'
        )));
    }

    private function getTopIpkStudentsByDepartment($departmentId, $academicYearId)
    {
        $academicYear = AcademicYear::find($academicYearId) ?? AcademicYear::where('status', 1)->first();
        if (!$academicYear) {
            return collect([]);
        }

        // untuk semua semester
        // $grades = Grade::where('academic_year_id', $academicYear->id)
        // untuk semester aktif
        $grades = Grade::where('academic_year_id', 1)
            ->whereNotNull('nhuruf')
            ->whereHas('student', fn($q) => $q->where('department_id', $departmentId))
            ->with(['student', 'schedule.course'])
            ->get();

        $studentIpk = $grades->groupBy('student_id')->map(function ($grades) {
            $totalSks = $totalNilai = 0;
            foreach ($grades as $grade) {
                $sks = $grade->schedule->course->sks ?? 0;
                $nilai = Grade::convertNhurufToAngka($grade->nhuruf);
                if ($sks && $nilai !== null) {
                    $totalSks += $sks;
                    $totalNilai += $sks * $nilai;
                }
            }
            $ipk = $totalSks ? $totalNilai / $totalSks : 0;
            $student = $grades->first()->student;

            return [
                'name' => optional($student)->nama_mhs ?? 'Unknown',
                'ipk' => number_format($ipk, 2),
                'nim' => optional($student)->nim ?? 'Unknown',
                'department' => optional($student->department)->nama ?? 'Unknown'
            ];
        })->sortByDesc('ipk')->take(3)->values();

        return $studentIpk;
    }

    private function getIpkDistribution($departmentId, $academicYearId)
    {
        $academicYear = AcademicYear::find($academicYearId) ?? AcademicYear::where('status', 1)->first();
        if (!$academicYear) {
            return ['1.0-2.0' => 0, '2.0-3.0' => 0, '3.0-4.0' => 0];
        }

        $grades = Grade::where('academic_year_id', $academicYear->id)
            ->whereNotNull('nhuruf')
            ->whereHas('student', fn($q) => $q->where('department_id', $departmentId))
            ->with(['student', 'schedule.course'])
            ->get();

        $studentIpk = $grades->groupBy('student_id')->map(function ($grades) {
            $totalSks = $totalNilai = 0;
            foreach ($grades as $grade) {
                $sks = $grade->schedule->course->sks ?? 0;
                $nilai = Grade::convertNhurufToAngka($grade->nhuruf);
                if ($sks && $nilai !== null) {
                    $totalSks += $sks;
                    $totalNilai += $sks * $nilai;
                }
            }
            return $totalSks ? $totalNilai / $totalSks : 0;
        });

        // Kelompokkan IPK
        $distribution = [
            '1.0-2.0' => 0,
            '2.0-3.0' => 0,
            '3.0-4.0' => 0,
        ];

        foreach ($studentIpk as $ipk) {
            if ($ipk >= 1.0 && $ipk < 2.0) {
                $distribution['1.0-2.0']++;
            } elseif ($ipk >= 2.0 && $ipk < 3.0) {
                $distribution['2.0-3.0']++;
            } elseif ($ipk >= 3.0 && $ipk <= 4.0) {
                $distribution['3.0-4.0']++;
            }
        }

        return $distribution;
    }

}
