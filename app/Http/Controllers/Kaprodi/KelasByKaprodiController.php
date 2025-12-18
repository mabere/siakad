<?php

namespace App\Http\Controllers\Kaprodi;

use App\Models\Kelas;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KelasByKaprodiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $lecturer = Lecturer::where('user_id', $user->id)->firstOrFail();
        $department = Department::where('head_id', $lecturer->id)->firstOrFail();
        $departmentId = $department->id;

        // Ambil semua kelas di department Kaprodi dengan pagination
        $kelas = Kelas::where('department_id', $departmentId)
            ->whereNull('deleted_at')
            ->with(['department', 'lecturer'])
            ->paginate(10); // 10 kelas per halaman

        return view('kaprodi.kelas.index', [
            'kelas' => $kelas,
            'department' => $department,
        ]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $lecturer = Lecturer::where('user_id', $user->id)->firstOrFail();
        $department = Department::where('head_id', $lecturer->id)->firstOrFail();
        $departmentId = $department->id;

        // Ambil kelas berdasarkan ID dan pastikan milik department Kaprodi
        $item = Kelas::with(['department', 'lecturer', 'students'])
            ->where('id', $id)
            ->where('department_id', $departmentId)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $mahasiswa = $item->students; // Ambil mahasiswa dari relasi
        $jumlahMahasiswa = $mahasiswa->count();

        return view('kaprodi.kelas.show', [
            'item' => $item,
            'mahasiswa' => $mahasiswa,
            'jumlahMahasiswa' => $jumlahMahasiswa,
            'idKelas' => $id,
            'department' => $department,
        ]);
    }

    public function showAddStudents(Request $request, $id)
    {
        $user = $request->user();
        $lecturer = Lecturer::where('user_id', $user->id)->firstOrFail();
        $department = Department::where('head_id', $lecturer->id)->firstOrFail();
        $departmentId = $department->id;

        $kelas = Kelas::where('id', $id)
            ->where('department_id', $departmentId)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $mahasiswa = Student::whereNull('kelas_id')
            ->where('department_id', $kelas->department_id)
            ->whereNull('deleted_at')
            ->with(['department'])
            ->get();

        return view('kaprodi.kelas.add-students', [
            'kelas' => $kelas,
            'mahasiswa' => $mahasiswa,
        ]);
    }

    public function storeStudents(Request $request, $id)
    {
        $user = $request->user();
        $lecturer = Lecturer::where('user_id', $user->id)->firstOrFail();
        $department = Department::where('head_id', $lecturer->id)->firstOrFail();
        $departmentId = $department->id;

        $kelas = Kelas::where('id', $id)
            ->where('department_id', $departmentId)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        Student::whereIn('id', $request->student_ids)
            ->where('department_id', $kelas->department_id)
            ->whereNull('deleted_at')
            ->update(['kelas_id' => $kelas->id]);

        return redirect()->route('kaprodi.kelas.show', $id)
            ->with('status', 'Mahasiswa berhasil ditambahkan ke kelas.');
    }

    public function removeStudent(Request $request, $kelasId, $studentId)
    {
        $user = $request->user();
        $lecturer = Lecturer::where('user_id', $user->id)->firstOrFail();
        $department = Department::where('head_id', $lecturer->id)->firstOrFail();
        $departmentId = $department->id;

        $kelas = Kelas::where('id', $kelasId)
            ->where('department_id', $departmentId)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $student = Student::where('id', $studentId)
            ->where('department_id', $departmentId)
            ->whereNull('deleted_at')
            ->firstOrFail();

        if ($student->kelas_id != $kelas->id) {
            return redirect()->route('kaprodi.kelas.show', $kelasId)
                ->with('error', 'Mahasiswa tidak terdaftar di kelas ini.');
        }

        $student->update(['kelas_id' => null]);

        return redirect()->route('kaprodi.kelas.show', $kelasId)
            ->with('status', 'Mahasiswa berhasil dikeluarkan dari kelas.');
    }
}