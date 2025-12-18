<?php

namespace App\Http\Controllers\Backend;


use App\Models\User;
use App\Models\Lecturer;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Imports\LecturerImport;
use App\Exports\LecturersExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class LecturerController extends Controller
{
    public function index()
    {
        $dosen = Department::all();
        return view('backend.dosen.index', compact('dosen'));
    }

    public function listDosen($departmentId)
    {
        $department = Department::findOrFail($departmentId); // Temukan department berdasarkan ID
        $dosen = Lecturer::where('department_id', $departmentId)->get(); // Filter dosen berdasarkan department_id
        return view('backend.dosen.list', compact('dosen', 'department'));
    }


    public function show($id)
    {
        $dosen = Lecturer::with('department')->find($id);
        return view('backend.dosen.show', compact('dosen'));
    }

    public function create()
    {
        $department = Department::all();
        return view('backend.dosen.create', compact('department'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_dosen' => 'required|string',
            'department_id' => 'required',
            'nidn' => 'required|unique:lecturers,nidn',
            'email' => 'required',
            'address' => 'required',
            'telp' => 'required',
            'tpl' => 'required',
            'tgl' => 'required',
        ]);

        $data = new Lecturer;

        $data->nama_dosen = $request->nama_dosen;
        $data->department_id = $request->department_id;
        $data->nidn = $request->nidn;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->telp = $request->telp;
        $data->tpl = $request->tpl;
        $data->tgl = $request->tgl;
        $data->save();
        return redirect()->route('admin.dosen.by.department', $data->department->id)->with('success', 'Data Dosen berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $dosen = Lecturer::with('department')->findOrFail($id);
        return view('backend.dosen.edit', compact('dosen'));
    }

    public function update(Request $request, $id)
    {
        $dosen = Lecturer::findOrFail($id);
        $request->validate([
            'nama_dosen' => 'required|string',
            'department_id' => 'required',
            'nidn' => 'required',
            'email' => 'nullable',
        ]);

        $dosen->update([
            'nama_dosen' => $request->nama_dosen,
            'department_id' => $request->department_id,
            'nidn' => $request->nidn,
            'email' => $request->email,
            'address' => $request->address,
            'scholar_google' => $request->scholar_google,
            'gender' => $request->gender,
            'telp' => $request->telp,
            'tpl' => $request->tpl,
            'tgl' => $request->tgl,
        ]);
        return redirect()->route('admin.dosen.by.department', $dosen->department->id)->with('update', 'Data Dosen berhasil diedit.');
    }

    public function destroy($id)
    {
        $dosen = Lecturer::findOrFail($id);

        // Hapus user yang terkait
        if ($dosen->user) {
            $dosen->user->delete();
        }

        // Hapus data dosen dari database
        $dosen->delete();

        return redirect()->route('admin.dosen.by.department', $dosen->department->id)->with('success', 'Data Dosen berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx|max:100',
        ]);

        Excel::import(new LecturerImport, $request->file('file')->store('temp'));

        return back()->with('success', 'Data Dosen berhasil diimpor.');
    }


    public function assignDosenUser(Lecturer $dosen)
    {
        $user = new User();
        $user->name = $dosen->nama_dosen;
        $user->email = $dosen->email;
        $user->password = Hash::make($dosen->nidn);
        $user->save();
        $user->roles()->attach(5);
        $dosen->user_id = $user->id;
        $dosen->save();

        return redirect()->back()->with('success', 'Akun Dosen berhasil dibuat/diaktifkan.');
    }

    public function export($id)
    {
        return Excel::download(new LecturersExport($id), 'lecturers-department-' . $id . '.xlsx');
    }

}