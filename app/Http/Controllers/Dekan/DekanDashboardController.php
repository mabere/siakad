<?php

namespace App\Http\Controllers\Dekan;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Kegiatan;
use App\Models\Lecturer;
use App\Models\Department;
use App\Models\LetterType;
use App\Models\AcademicYear;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Models\LetterRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DekanDashboardController extends Controller
{
    public function indexProdi()
    {
        $user = Auth::user();
        if (!$user || !$user->lecturer || !$user->lecturer->department) {
            return redirect()->back()->with('error', 'Data staff atau department tidak ditemukan.');
        }
        $facultyId = $user->lecturer->department->faculty_id;
        $departments = Department::where('faculty_id', $facultyId)->get();
        return view('dekan.prodi.index', compact('departments'));
    }

    public function show($id)
    {
        $user = Auth::user();
        if (!$user || !$user->lecturer || !$user->lecturer->department) {
            return redirect()->back()->with('error', 'Data staff atau department tidak ditemukan.');
        }

        $department = Department::with('lecturer')->findOrFail($id);
        $staffFacultyId = $user->lecturer->department->faculty_id;

        // Cek apakah department berada di fakultas staff
        if ($department->faculty_id !== $staffFacultyId) {
            return redirect()->route('staff.departments.index')->with('error', 'Anda tidak memiliki akses ke department ini.');
        }

        return view('dekan.prodi.show', compact('department'));
    }

    public function studentStatistics()
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
        $semesterAktif = (now()->toDate() >= $activeYear->start_date && now()->toDate() <= $activeYear->end_date);

        // Ambil semua departemen di fakultas user dengan relasi students, courses, dan semesterStatuses
        $departments = Department::where('faculty_id', $facultyId)
            ->with(['students.studentSemesterStatus', 'courses'])
            ->get();

        // Hitung statistik per departemen
        $statistics = $departments->map(function ($department) use ($activeYear, $semesterAktif) {
            $students = $department->students;
            $courses = $department->courses;

            $totalSks = $courses->sum('sks');

            $activeStudents = $students->filter(function ($student) use ($activeYear) {
                $status = $student->studentSemesterStatus;
                return $status && $status->academic_year_id == $activeYear->id && $status->status == 'aktif';
            })->count();

            $jenjangDistribution = $students->groupBy('jenjang')->map->count();

            return [
                'department' => $department,
                'total_students' => $students->count(),
                'total_sks' => $totalSks,
                'active_students' => $activeStudents,
                'jenjang_distribution' => $jenjangDistribution,
            ];
        });

        // Hitung total SKS keseluruhan untuk fakultas
        $totalFacultySks = $departments->sum(function ($department) {
            return $department->courses->sum('sks');
        });

        return view('dekan.mhs.index', compact('statistics', 'facultyId', 'activeYear', 'semesterAktif'));
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

        // Ambil departemen berdasarkan ID
        $department = Department::with(['students.studentSemesterStatus'])->findOrFail($department);
        if ($department->faculty_id != $lecturer->faculty_id) {
            abort(403, 'Anda tidak memiliki akses ke data departemen ini.');
        }

        $activeYear = AcademicYear::where('status', true)->firstOrFail();
        $semesterAktif = (now()->toDate() >= $activeYear->start_date && now()->toDate() <= $activeYear->end_date);

        // Kelompokkan mahasiswa berdasarkan entry_year
        $students = $department->students;
        $studentStatsByYear = $students->groupBy('entry_year')->map(function ($group) use ($activeYear) {
            $activeStudents = $group->filter(function ($student) use ($activeYear) {
                $status = $student->studentSemesterStatus;
                return $status && $status->academic_year_id == $activeYear->id && $status->status == 'aktif';
            })->count();

            $jenjangDistribution = $group->groupBy('jenjang')->map->count();

            return [
                'year' => $group->first()->entry_year,
                'total_students' => $group->count(),
                'active_students' => $activeStudents,
                'jenjang_distribution' => $jenjangDistribution,
            ];
        })->sortBy('year'); // Urutkan berdasarkan tahun secara ascending

        // Total SKS kurikulum departemen
        $totalSks = $department->courses->sum('sks');

        return view('dekan.mhs.show', compact('department', 'studentStatsByYear', 'activeYear', 'semesterAktif', 'totalSks'));
    }
}