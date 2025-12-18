<?php

namespace App\Http\Controllers\Dekan;

use App\Models\Grade;
use App\Models\Department;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AcademicByDekanController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->roles->contains('name', 'dekan')) {
            abort(403, 'Hanya dekan yang dapat mengakses halaman ini.');
        }

        $lecturer = $user->lecturer;
        if (!$lecturer || !$lecturer->faculty_id) {
            abort(403, 'Data fakultas tidak ditemukan untuk user ini.');
        }

        $facultyId = $lecturer->faculty_id;
        $departments = Department::where('faculty_id', $facultyId)->with('students')->get();
        $totalStudents = $departments->sum(function ($dept) {
            return $dept->students->count();
        });
        $activeStudents = $departments->sum(function ($dept) {
            return $dept->students->filter(function ($student) {
                $status = $student->studentSemesterStatus;
                return $status && $status->status == 'aktif';
            })->count();
        });
        $avgIpk = $departments->avg(function ($dept) {
            return $dept->students->avg('ipk') ?? 0; // Asumsikan ada kolom ipk di students
        });

        return view('dekan.akademic.report', compact('totalStudents', 'activeStudents', 'avgIpk', 'facultyId'));
    }


    public function academicReport()
    {
        $user = Auth::user();

        if (!$user->roles->contains('name', 'dekan')) {
            abort(403, 'Hanya dekan yang dapat mengakses halaman ini.');
        }

        $lecturer = $user->lecturer;
        if (!$lecturer || !$lecturer->faculty_id) {
            abort(403, 'Data fakultas tidak ditemukan untuk user ini.');
        }

        $facultyId = $lecturer->faculty_id;
        $activeYear = AcademicYear::where('status', true)->firstOrFail();

        // Ambil semua departemen di fakultas user dengan relasi students
        $departments = Department::where('faculty_id', $facultyId)
            ->with(['students.studentSemesterStatus'])
            ->get();

        // Hitung total mahasiswa
        $totalStudents = $departments->sum(function ($dept) {
            return $dept->students->count();
        });

        // Hitung mahasiswa aktif
        $activeStudents = $departments->sum(function ($dept) use ($activeYear) {
            return $dept->students->filter(function ($student) use ($activeYear) {
                $status = $student->studentSemesterStatus;
                return $status && $status->academic_year_id == $activeYear->id && $status->status == 'aktif';
            })->count();
        });

        // Hitung rata-rata IPK
        $allStudents = $departments->flatMap(function ($dept) {
            return $dept->students;
        });

        $totalIpk = 0;
        $studentsWithIpk = 0;

        foreach ($allStudents as $student) {
            $ipk = Grade::calculateIpk($student->id, $activeYear->id);
            if ($ipk !== null && $ipk > 0) {
                $totalIpk += $ipk;
                $studentsWithIpk++;
            }
        }

        $avgIpk = $studentsWithIpk > 0 ? $totalIpk / $studentsWithIpk : 0;

        return view('dekan.academic.report', compact('totalStudents', 'activeStudents', 'avgIpk', 'facultyId'));
    }


    public function studentDetails($department)
    {
        $user = Auth::user();

        if (!$user->roles->contains('name', 'dekan')) {
            abort(403, 'Hanya dekan yang dapat mengakses halaman ini.');
        }

        $lecturer = $user->lecturer;
        if (!$lecturer || !$lecturer->faculty_id) {
            abort(403, 'Data fakultas tidak ditemukan untuk user ini.');
        }

        $department = Department::with(['students.studentSemesterStatus'])->findOrFail($department);
        if ($department->faculty_id != $lecturer->faculty_id) {
            abort(403, 'Anda tidak memiliki akses ke data departemen ini.');
        }

        $activeYear = AcademicYear::where('status', true)->firstOrFail();
        $semesterAktif = (now()->toDate() >= $activeYear->start_date && now()->toDate() <= $activeYear->end_date);

        $students = $department->students;

        $studentStatsByYear = $students->groupBy('entry_year')->map(function ($group) use ($activeYear) {
            $activeStudents = $group->filter(function ($student) use ($activeYear) {
                $status = $student->studentSemesterStatus;
                return $status && $status->academic_year_id == $activeYear->id && $status->status == 'aktif';
            })->count();

            $jenjangDistribution = $group->groupBy('jenjang')->map->count();

            $totalIpk = 0;
            $studentsWithIpk = 0;
            foreach ($group as $student) {
                $ipk = Grade::calculateIpk($student->id);


                if ($ipk !== null && $ipk > 0) {
                    $totalIpk += (float) $ipk;
                    $studentsWithIpk++;
                }
            }
            $avgIpk = $studentsWithIpk > 0 ? number_format($totalIpk / $studentsWithIpk, 2) : '0.00';

            return [
                'year' => $group->first()->entry_year,
                'total_students' => $group->count(),
                'active_students' => $activeStudents,
                'jenjang_distribution' => $jenjangDistribution,
                'average_ipk' => $avgIpk
            ];
        })->sortBy('year');

        $totalSks = $department->courses->sum('sks');

        return view('dekan.mhs.show', compact('department', 'studentStatsByYear', 'activeYear', 'semesterAktif', 'totalSks'));
    }

}