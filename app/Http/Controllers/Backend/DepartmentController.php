<?php

namespace App\Http\Controllers\Backend;

use App\Models\Faculty;
use App\Models\Lecturer;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DepartmentController extends Controller
{
    public function index()
    {
        $prodi = Department::all();
        return view('backend.prodi.index', compact('prodi'));
    }

    public function show($id)
    {
        $department = Department::with(['faculty', 'kaprodiLecturer.user'])->findOrFail($id);
        $lecturers = Lecturer::where('department_id', $department->id)
            ->whereNotNull('user_id')
            ->orderBy('nama_dosen')
            ->get();

        return view('backend.prodi.show', compact('department', 'lecturers'));
    }

    public function assignKaprodi(Request $request, $id)
    {
        $request->validate([
            'head_id' => 'required|exists:lecturers,id',
        ]);

        $department = Department::findOrFail($id);
        $department->head_id = $request->head_id;
        $department->save();

        return redirect()->route('admin.prodi.show', $id)->with('success', 'Kaprodi berhasil ditetapkan.');
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        $faculty = Faculty::all();
        return view('backend.prodi.edit', compact('department', 'faculty'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'faculty_id' => 'required',
            'nama' => 'required',
            'kaprodi' => 'required',
            'visi' => 'required',
            'misi' => 'required',
            'jenjang' => 'required',
        ]);
        $department = Department::findOrFail($id);
        $department->update([
            'faculty_id' => $request->faculty_id,
            'nama' => $request->nama,
            'kaprodi' => $request->kaprodi,
            'visi' => $request->visi,
            'misi' => $request->misi,
            'jenjang' => $request->jenjang,
        ]);

        return redirect()->route('admin.prodi.index')->with('success', 'Data Program Studi berhasil diedit.');
    }

    public function create()
    {
        $faculty = Faculty::all();
        return view('backend.prodi.create', compact('faculty'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'faculty_id' => 'required',
            'nama' => 'required',
            'kaprodi' => 'required',
            'visi' => 'required',
            'jenjang' => 'required',
            'misi' => 'required',
        ]);

        $department = new Department;
        $department->faculty_id = $request->faculty_id;
        $department->nama = $request->nama;
        $department->kaprodi = $request->kaprodi;
        $department->jenjang = $request->jenjang;
        $department->visi = $request->visi;
        $department->misi = $request->misi;
        $department->save();

        return redirect()->route('admin.prodi.index')->with('success', 'Data Program Studi berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();
        return redirect()->route('admin.prodi.index')->with('success', 'Data Program Studi berhasil dihapus.');
    }
}