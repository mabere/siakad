<?php

namespace App\Http\Controllers\Backend;

use App\Models\StudyPlan;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StudentSemesterStatus;
use Illuminate\Pagination\LengthAwarePaginator;

class StudentSemesterStatusController extends Controller
{
    public function index(Request $request)
    {
        $currentAcademicYear = AcademicYear::where('status', 1)->first();

        if (!$currentAcademicYear) {
            return redirect()->back()->with('error', 'Academic year not found.');
        }

        $termActive = $currentAcademicYear->semester;

        // Ambil data study plan untuk academic_year aktif
        $studyPlans = StudyPlan::with(['student', 'academicYear'])
            ->where('academic_year_id', $currentAcademicYear->id)
            ->get();

        // Kelompokkan data study plan berdasarkan student_id
        $grouped = $studyPlans->groupBy('student_id');

        // Mapping untuk menghasilkan data per mahasiswa
        $data = $grouped->map(function ($plans, $studentId) use ($currentAcademicYear) {
            $student = $plans->first()->student;
            $currentSemester = $student->getCurrentSemester($currentAcademicYear->ta, $currentAcademicYear->semester);

            // Tentukan status berdasarkan study_plans (bisa diperluas untuk status lain)
            $status = $plans->count() > 0 ? 'aktif' : 'do';

            // Simpan atau perbarui status semester di student_semester_status
            $student->statusSemesters()->updateOrCreate(
                [
                    'academic_year_id' => $currentAcademicYear->id,
                    'semester' => $currentSemester,
                ],
                [
                    'status' => $status,
                    'effective_date' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $latestUpdate = $plans->max('updated_at');

            return (object) [
                'student_id' => $studentId,
                'student_name' => $student->nama_mhs,
                'academic_year' => $currentAcademicYear->ta,
                'current_semester' => $currentSemester,
                'status' => $status,
                'latest_update' => $latestUpdate,
            ];
        })->values();

        $data = $data->sortBy('current_semester')->values();

        $dataArray = $data->toArray();

        // Konfigurasi pagination
        $page = $request->get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;

        $paginatedData = new LengthAwarePaginator(
            array_slice($dataArray, $offset, $perPage, true),
            count($dataArray),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        return view(
            'backend.mhs.status.index',
            [
                'data' => $paginatedData,
                'currentAcademicYear' => $currentAcademicYear
            ]
        );
    }

}