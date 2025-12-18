<?php

namespace App\Http\Controllers\Backend;

use App\Models\Role;
use App\Models\User;
use App\Models\Student;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Imports\StudentImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index()
    {
        $department = Department::all();
        return view('backend.mhs.indexs', compact('department'));
    }
    public function listMhsprodi($id)
    {
        $department = Department::findOrFail($id); // Temukan department berdasarkan ID
        $mhs = Student::where('department_id', $id)->paginate(10);
        return view('backend.mhs.list_mhs_prodi', compact('mhs', 'department'));

    }

    public function show($id)
    {
        $student = Student::with('department')->findOrFail($id);
        return view('backend.mhs.show', compact('student'));
    }

    public function create($department_id)
    {
        $department = Department::findOrFail($department_id);
        return view('backend.mhs.create', compact('department'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mhs' => 'required|string',
            'department_id' => 'nullable',
            'nim' => 'required|unique:students,nim',
            'email' => 'required|email|unique:students,email',
            'address' => 'nullable|string',
            'telp' => 'nullable|string',
            'tpl' => 'nullable|string',
            'tgl' => 'nullable|date'
        ]);

        $data = new Student;

        $data->nama_mhs = $request->nama_mhs;
        $data->department_id = $request->department_id; // Set dari parameter
        $data->nim = $request->nim;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->telp = $request->telp;
        $data->tpl = $request->tpl;
        $data->tgl = $request->tgl;
        $data->save();
        return redirect()->route('admin.mhs.by.department', $request->department_id)->with('success', 'Data Mahasiswa berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $student = Student::with('department', 'kelas')->findOrFail($id);
        return view('backend.mhs.edit', compact('student'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $request->validate([
            'nama_mhs' => 'required|string',
            'nim' => 'required|unique:students,nim,' . $id,
            'email' => 'required|email|unique:students,email,' . $id,
            'photo' => 'nullable|image|max:2048',
            'address' => 'nullable|string',
            'telp' => 'nullable|string',
            'tpl' => 'nullable|string',
            'tgl' => 'required|date',
        ]);

        if ($request->hasFile('photo')) {
            $newPhoto = $request->file('photo');
            if ($student->photo) {
                Storage::delete('public/images/mhs/' . $student->photo);
            }
            $photoName = $newPhoto->hashName();
            $newPhoto->storeAs('public/images/mhs', $photoName);
            $student->photo = $photoName;
        }

        $student->update([
            'nama_mhs' => $request->nama_mhs,
            'nim' => $request->nim,
            'email' => $request->email,
            'address' => $request->address,
            'telp' => $request->telp,
            'tpl' => $request->tpl,
            'tgl' => $request->tgl,
        ]);

        return redirect()->route('admin.mhs.by.department', $student->department_id)->with('success', 'Data Mahasiswa berhasil diedit!');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        if ($student->photo) {
            Storage::delete('public/images/mhs/' . $student->photo);
        }
        $departmentId = $student->department_id;
        $student->delete();
        return redirect()->route('admin.mhs.by.department', $departmentId)->with('success', 'Mahasiswa berhasil dihapus!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx|max:100',
        ]);
        Excel::import(new StudentImport, $request->file('file')->store('temp'));
        return back()->with('success', 'Data Mahasiswa berhasil diimpor.');
    }

    public function assignUser(Student $mhs)
    {
        $user = new User();
        $user->name = $mhs->nama_mhs;
        $user->email = $mhs->email;
        $user->password = Hash::make($mhs->nim);
        $user->save();
        $user->roles()->attach(6);
        $mhs->user_id = $user->id;
        $mhs->save();
        return redirect()->back()->with('success', 'Akun Mahasiswa berhasil dibuat/diaktifkan.');
    }

    public function assignMultipleUsers(Request $request)
    {
        $request->validate([
            'selected_students' => 'required|array',
            'selected_students.*' => 'exists:students,id'
        ]);
        try {
            DB::beginTransaction();
            foreach ($request->selected_students as $studentId) {
                $student = Student::findOrFail($studentId);
                if ($student->user_id) {
                    continue;
                }
                $user = User::create([
                    'name' => $student->nama_mhs,
                    'email' => $student->email,
                    'password' => Hash::make($student->nim)
                ]);
                $user->roles()->attach(6); // Student role ID
                $student->update(['user_id' => $user->id]);
            }
            DB::commit();
            return redirect()->back()->with('success', 'Akun mahasiswa berhasil dibuat untuk semua yang dipilih.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal membuat akun: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function unassignUser($id)
    {
        $student = Student::findOrFail($id);

        if ($student->user_id) {
            $user = User::find($student->user_id);

            if ($user) {
                // Hapus peran student dari user
                $user->roles()->detach(Role::where('name', 'student')->first());

                // Hapus user_id dari tabel mahasiswa
                $student->user_id = null;
                $student->save();

                // Jika ingin menghapus user dari sistem (opsional)
                $user->delete();
            }
        }

        return redirect()->back()->with('success', 'Mahasiswa berhasil di-unassign sebagai user.');
    }

}
