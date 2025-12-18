<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Department;
use Illuminate\Http\Request;

class MhsByKaprodiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $lecturer = Lecturer::where('user_id', $user->id)->firstOrFail();
        $department = Department::where('head_id', $lecturer->id)->firstOrFail();
        $departmentId = $department->id;

        // Ambil semua mahasiswa di department Kaprodi
        $mahasiswa = Student::where('department_id', $departmentId)
            ->whereNull('deleted_at')
            ->with(['department', 'kelas', 'advisor'])
            ->paginate(10);

        return view('kaprodi.mhs.index', [
            'mahasiswa' => $mahasiswa,
            'department' => $department,
        ]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $lecturer = Lecturer::where('user_id', $user->id)->firstOrFail();
        $department = Department::where('head_id', $lecturer->id)->firstOrFail();
        $departmentId = $department->id;

        // Ambil detail mahasiswa berdasarkan ID dan pastikan di department Kaprodi
        $mahasiswa = Student::where('id', $id)
            ->where('department_id', $departmentId)
            ->whereNull('deleted_at')
            ->with(['department', 'kelas', 'advisor'])
            ->firstOrFail();

        return view('kaprodi.mhs.show', [
            'mahasiswa' => $mahasiswa,
            'department' => $department,
        ]);
    }
}