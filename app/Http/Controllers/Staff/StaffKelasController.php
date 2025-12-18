<?php

namespace App\Http\Controllers\Staff;

use App\Models\Kelas;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StaffKelasController extends Controller
{
    public function index()
    {
        $staff = Auth::user()->employee;

        if (!$staff) {
            return redirect()->route('dashboard')->with('error', 'Data pegawai tidak ditemukan.');
        }
        $kelas = collect();
        $info = null;
        if ($staff->level === 'fakultas') {
            $kelas = Kelas::all();
            $info = 'Menampilkan semua kelas karena Anda berada di tingkat fakultas.';
        } elseif ($staff->level === 'department' && $staff->department_id) {
            $kelas = Kelas::where('department_id', $staff->department_id)->get();
            $info = 'Menampilkan kelas berdasarkan departemen Anda.';
        } else {
            $info = 'Tidak ada data kelas yang bisa ditampilkan karena data departemen tidak lengkap.';
        }
        $routePrefix = explode('.', request()->route()->getName())[0] . '.kelas.';
        return view('staff.kelas.index', compact('kelas', 'routePrefix', 'info'));
    }

    public function create()
    {
        $staff = Auth::user()->employee;
        $prodi = Department::where('id', $staff->department_id)->get();
        $dosen = Lecturer::where('department_id', $staff->department_id)->get();
        return view('staff.kelas.create', compact('prodi', 'dosen'));
    }

    public function store(Request $request)
    {
        $staff = Auth::user()->employee;
        $request->validate([
            'lecturer_id' => 'required|exists:lecturers,id',
            'name' => 'required',
            'angkatan' => 'required',
            'total' => 'required',
        ]);
        $kelas = new Kelas([
            'department_id' => $staff->department_id,
            'lecturer_id' => $request->lecturer_id,
            'name' => $request->name,
            'angkatan' => $request->angkatan,
            'total' => $request->total,
        ]);
        $kelas->save();
        return redirect()->route('staff.kelas.index')->with('success', 'Data Kelas berhasil ditambahkan!');
    }

    public function show($id)
    {
        $staff = Auth::user()->employee;
        $item = Kelas::with(['department', 'lecturer'])->findOrFail($id);

        if ($staff->level === 'department' && $item->department_id !== $staff->department_id) {
            return redirect()->route('staff.kelas.index')->with('error', 'Anda tidak memiliki akses ke kelas ini.');
        }
        $mahasiswa = Student::where('kelas_id', $id)->get();
        $jumlahMahasiswa = count($mahasiswa);
        $idKelas = $id;
        return view('staff.kelas.show')->with([
            'item' => $item,
            'mahasiswa' => $mahasiswa,
            'jumlahMahasiswa' => $jumlahMahasiswa,
            'idKelas' => $idKelas
        ]);
    }

    public function edit($id)
    {
        $staff = Auth::user()->employee;
        $kelas = Kelas::findOrFail($id);

        if ($staff->level === 'department' && $kelas->department_id !== $staff->department_id) {
            return redirect()->route('staff.kelas.index')->with('error', 'Anda tidak memiliki akses ke kelas ini.');
        }
        $prodi = Department::where('id', $staff->department_id)->get();
        $dosen = Lecturer::where('department_id', $staff->department_id)->get();
        return view('staff.kelas.edit', compact('kelas', 'prodi', 'dosen'));
    }

    public function update(Request $request, $id)
    {
        $staff = Auth::user()->employee;
        $kelas = Kelas::findOrFail($id);
        if ($staff->level === 'department' && $kelas->department_id !== $staff->department_id) {
            return redirect()->route('staff.kelas.index')->with('error', 'Anda tidak memiliki akses untuk mengedit kelas ini.');
        }
        $request->validate([
            'lecturer_id' => 'required|exists:lecturers,id',
            'name' => 'required',
            'angkatan' => 'required',
            'total' => 'required',
        ]);
        $kelas = Kelas::findOrFail($id);
        $kelas->department_id = $staff['department_id'];
        $kelas->lecturer_id = $request['lecturer_id'];
        $kelas->name = $request['name'];
        $kelas->angkatan = $request['angkatan'];
        $kelas->total = $request['total'];
        $kelas->save();
        return redirect()->route('staff.kelas.index')->with('success', 'Data Kelas berhasil diperbaharui.');
    }

    public function destroy($id)
    {
        $staff = Auth::user()->employee;
        $kelas = Kelas::findOrFail($id);
        if ($staff->level === 'department' && $kelas->department_id !== $staff->department_id) {
            return redirect()->route('staff.kelas.index')->with('error', 'Anda tidak memiliki akses untuk menghapus kelas ini.');
        }
        $kelas->delete();
        return redirect()->route('staff.kelas.index')->with('success', 'Data Kelas berhasil dihapus.');
    }

    public function showAddStudents($id)
    {
        $staff = Auth::user()->employee;
        $kelas = Kelas::findOrFail($id);
        if ($staff->level === 'department' && $kelas->department_id !== $staff->department_id) {
            return redirect()->route('staff.kelas.index')->with('error', 'Anda tidak memiliki akses ke kelas ini.');
        }
        $mahasiswa = Student::whereNull('kelas_id')
            ->where('department_id', $kelas->department_id)
            ->get();
        return view('staff.kelas.add-students', [
            'kelas' => $kelas,
            'mahasiswa' => $mahasiswa,
        ]);
    }

    public function storeStudents(Request $request, $id)
    {
        $staff = Auth::user()->employee;
        $kelas = Kelas::findOrFail($id);
        if ($staff->level === 'department' && $kelas->department_id !== $staff->department_id) {
            return redirect()->route('staff.kelas.index')->with('error', 'Anda tidak memiliki akses ke kelas ini.');
        }
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);
        $updated = Student::whereIn('id', $request->student_ids)
            ->where('department_id', $kelas->department_id)
            ->whereNull('kelas_id')
            ->update(['kelas_id' => $kelas->id]);

        $message = $updated > 0
            ? "$updated mahasiswa berhasil ditambahkan ke kelas."
            : "Tidak ada mahasiswa yang ditambahkan. Pastikan mereka belum terdaftar di kelas lain.";

        return redirect()->route('staff.kelas.show', $kelas->id)
            ->with('status', $message);
    }

    public function removeStudent(Request $request, $kelasId, $studentId)
    {
        $kelas = Kelas::findOrFail($kelasId);
        $student = Student::findOrFail($studentId);
        if ($student->kelas_id != $kelas->id) {
            return redirect()->route('staff.kelas.show', $kelasId)
                ->with('error', 'Mahasiswa tidak terdaftar di kelas ini.');
        }
        $student->update(['kelas_id' => null]);
        return redirect()->route('staff.kelas.show', $kelasId)
            ->with('status', 'Mahasiswa berhasil dikeluarkan dari kelas.');
    }

}