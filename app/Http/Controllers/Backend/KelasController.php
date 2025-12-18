<?php

namespace App\Http\Controllers\Backend;


use App\Models\Kelas;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KelasController extends Controller
{
    public function index()
    {
        $departments = Department::all();
        return view('backend.kelas.index', compact('departments'));
    }

    public function indexByDepartment($department_id)
    {
        $department = Department::findOrFail($department_id);
        $kelas = Kelas::where('department_id', $department_id)->get();
        return view('backend.kelas.by_department', compact('kelas', 'department'));
    }

    public function createKelasForDepartment($department_id)
    {
        $dosen = Lecturer::all();
        return view('backend.kelas.create', compact('dosen', 'department_id'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'advisor_id' => 'required|exists:lecturers,id',
            'name' => 'required',
            'angkatan' => 'required',
            'total' => 'required|numeric',
        ]);

        $kelas = new Kelas;
        $kelas->department_id = $request->department_id;
        $kelas->lecturer_id = $request->advisor_id;
        $kelas->name = $request->name;
        $kelas->angkatan = $request->angkatan;
        $kelas->total = $request->total;
        $kelas->save();

        return redirect()->route('admin.kelas.byDepartment', $kelas->department_id)
            ->with('success', 'Data Kelas berhasil ditambahkan!');
    }

    public function show($id)
    {
        $item = Kelas::with(['department', 'lecturer'])->findOrFail($id);
        $mahasiswa = Student::where('kelas_id', $id)->get();
        $jumlahMahasiswa = count($mahasiswa);
        $idKelas = $id;
        return view('backend.kelas.show')->with([
            'item' => $item,
            'mahasiswa' => $mahasiswa,
            'jumlahMahasiswa' => $jumlahMahasiswa,
            'idKelas' => $idKelas
        ]);
    }

    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);
        $dosen = Lecturer::all();
        return view('backend.kelas.edit', compact('kelas', 'dosen'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'advisor_id' => 'required|exists:lecturers,id',
            'name' => 'required',
            'angkatan' => 'required',
            'total' => 'required|numeric',
        ]);

        $kelas = Kelas::findOrFail($id);
        $kelas->lecturer_id = $request->advisor_id;
        $kelas->name = $request->name;
        $kelas->angkatan = $request->angkatan;
        $kelas->total = $request->total;
        $kelas->save();

        return redirect()->route('admin.kelas.byDepartment', $kelas->department_id)
            ->with('success', 'Data Kelas berhasil diperbaharui.');
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        if ($kelas->students()->count() > 0) {
            return redirect()->back()->with('error', 'Kelas tidak bisa dihapus karena masih ada mahasiswa terkait.');
        }

        $department_id = $kelas->department_id;
        $kelas->delete();

        return redirect()->route('admin.kelas.byDepartment', $department_id)
            ->with('success', 'Data Kelas berhasil dihapus.');
    }
}