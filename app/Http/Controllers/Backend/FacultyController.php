<?php

namespace App\Http\Controllers\Backend;

use App\Models\Faculty;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FacultyController extends Controller
{
    public function index()
    {
        $faculty = Faculty::all();
        $lecturers = Lecturer::whereNotNull('user_id')->orderBy('nama_dosen')->get();
        return view('backend.fakultas.index', compact('faculty', 'lecturers'));
    }

    public function show($id)
    {
        $faculty = Faculty::findOrFail($id);
        $lecturers = Lecturer::whereNotNull('user_id')->orderBy('nama_dosen')->get();
        return view('backend.fakultas.show', compact('faculty', 'lecturers'));
    }

    public function assignDekan(Request $request, $id)
    {
        $request->validate([
            'lecturer_id' => 'required|exists:lecturers,id',
        ]);

        $lecturer = Lecturer::with('user')->findOrFail($request->lecturer_id);
        $faculty = Faculty::findOrFail($id);

        // Update dekan_user_id pada tabel faculties
        $faculty->update([
            'dekan_user_id' => $lecturer->user_id,
            'dekan' => $lecturer->nama_dosen,
            'nip' => $lecturer->nip,
        ]);

        return redirect()->route('admin.faculty.show', $id)->with('success', 'Dekan berhasil ditetapkan.');
    }

    public function edit($id)
    {
        $faculty = Faculty::findOrFail($id);
        return view('backend.fakultas.edit', compact('faculty'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'dekan' => 'required',
            'visi' => 'required',
            'misi' => 'required',
        ]);
        $faculty = Faculty::findOrFail($id);
        $faculty->update([
            'nama' => $request->nama,
            'dekan' => $request->dekan,
            'visi' => $request->visi,
            'misi' => $request->misi,
        ]);

        return redirect()->route('admin.faculty.index')->with('success', 'Data Fakultas berhasil diedit.');
    }

    public function create()
    {
        return view('backend.fakultas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'dekan' => 'required',
            'visi' => 'required',
            'misi' => 'required',
        ]);
        $faculty = new Faculty;
        $faculty->nama = $request->nama;
        $faculty->dekan = $request->dekan;
        $faculty->visi = $request->visi;
        $faculty->misi = $request->misi;
        $faculty->save();
        return redirect()->route('admin.faculty.index')->with('success', 'Data Fakultas berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $faculty = Faculty::findOrFail($id);

        $faculty->delete();
        return redirect()->route('admin.faculty.index')->with('success', 'Data Fakultas berhasil dihapus.');
    }
}