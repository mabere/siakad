<?php

namespace App\Http\Controllers\Kaprodi;

use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DosenByKaprodiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $lecturer = Lecturer::where('user_id', $user->id)->firstOrFail();
        $department = Department::where('head_id', $lecturer->id)->firstOrFail();
        $departmentId = $department->id;

        // Ambil semua dosen di department Kaprodi
        $dosen = Lecturer::where('department_id', $departmentId)
            ->whereNull('deleted_at')
            ->with(['department'])
            ->get();

        return view('kaprodi.dosen.index', [
            'dosen' => $dosen,
            'department' => $department,
        ]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $lecturer = Lecturer::where('user_id', $user->id)->firstOrFail();
        $department = Department::where('head_id', $lecturer->id)->firstOrFail();
        $departmentId = $department->id;

        $dosen = Lecturer::where('id', $id)
            ->where('department_id', $departmentId)
            ->whereNull('deleted_at')
            ->with(['department'])
            ->firstOrFail();

        $advisees = $dosen->advisees()
            ->where('department_id', $departmentId)
            ->whereNull('deleted_at')
            ->with(['department', 'kelas'])
            ->paginate(10); // Anda bisa sesuaikan jumlah per halaman

        return view('kaprodi.dosen.show', [
            'dosen' => $dosen,
            'mahasiswa' => $advisees,
            'department' => $department,
        ]);
    }
}