<?php

namespace App\Http\Controllers\Staff;

use App\Models\Role;
use App\Models\User;
use App\Models\Lecturer;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Imports\LecturerImport;
use Illuminate\Support\Facades\DB;
use App\Imports\DosenImportByStaff;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentImportByStaff;
use Illuminate\Support\Facades\Storage;

class StaffDosenController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->employee) {
            return redirect()->route('dashboard')->with('error', 'Data employee tidak ditemukan.');
        }
        $dosen = collect();
        $departmentStaff = null; // Variabel ini hanya relevan untuk staff
        if ($user->employee->level === 'faculty') {
            if (!$user->employee->faculty_id) {
                return redirect()->route('dashboard')->with('error', 'Data fakultas KTU tidak ditemukan.');
            }
            $dosen = Lecturer::where('faculty_id', $user->employee->faculty_id)->get();
        } elseif ($user->employee->level === 'department') {
            $staffEmployee = $user->employee;
            if (!$staffEmployee->department) {
                return redirect()->route('dashboard')->with('error', 'Data departemen tidak ditemukan.');
            }
            $departmentStaff = $staffEmployee->department;
            $dosen = Lecturer::where('department_id', $staffEmployee->department_id)->get();
        } else {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses.');
        }
        return view('staff.dosen.index', compact('dosen', 'departmentStaff'));
    }

    public function show($id)
    {
        $user = Auth::user();
        if (!$user->employee) {
            return redirect()->back()->with('error', 'Data employee tidak ditemukan.');
        }
        $dosen = null;
        $hasAccess = false;
        if ($user->employee->level === 'faculty') {
            $dosen = Lecturer::where('id', $id)
                ->where('faculty_id', $user->employee->faculty_id)
                ->with('department')
                ->first();
            if ($dosen) {
                $hasAccess = true;
            }
        } elseif ($user->employee->level === 'department') {
            $staffDepartmentId = optional($user->employee)->department_id;
            if ($staffDepartmentId) {
                $dosen = Lecturer::with('department')
                    ->where('id', $id)
                    ->where('department_id', $staffDepartmentId)
                    ->first();
                if ($dosen) {
                    $hasAccess = true;
                }
            }
        }
        if (!$hasAccess) {
            return redirect()->route('staff.dosen.index')->with('error', 'Anda tidak memiliki akses ke data dosen ini.');
        }
        return view('staff.dosen.show', compact('dosen'));
    }

    public function create()
    {
        $department = optional(Auth::user()->employee)->department;
        if (!$department) {
            return redirect()->route('staff.dosen.index')->with('error', 'Data Dosen tidak ditemukan.');

        }
        return view('staff.dosen.create', compact('department'));
    }

    public function store(Request $request)
    {
        $staff = Auth::user()->employee;

        if (!$staff || !$staff->department) {
            return redirect()->route('staff.dosen.index')->with('error', 'Data departemen tidak ditemukan.');
        }

        $request->validate([
            'nama_dosen' => 'required|string',
            'nidn' => 'required|unique:lecturers,nidn',
            'email' => 'required',
            'address' => 'required',
            'telp' => 'required',
            'tpl' => 'required',
            'tgl' => 'required',
        ]);

        $data = new Lecturer;
        $data->nama_dosen = $request->nama_dosen;
        $data->department_id = $staff->department_id;
        $data->nidn = $request->nidn;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->telp = $request->telp;
        $data->tpl = $request->tpl;
        $data->tgl = $request->tgl;
        $data->save();
        return redirect()->route('staff.dosen.index')->with('success', 'Data Dosen berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $staffDepartmentId = optional(Auth::user()->employee)->department_id;

        if (!$staffDepartmentId) {
            return redirect()->back()->with('error', 'Data departemen tidak ditemukan.');
        }

        $dosen = Lecturer::where('id', $id)
            ->where('department_id', $staffDepartmentId)
            ->first();

        if (!$dosen) {
            return redirect()->route('staff.dosen.index')->with('error', 'Anda tidak memiliki akses ke data dosen ini.');
        }

        return view('staff.dosen.edit', compact('dosen'));
    }

    public function update(Request $request, $id)
    {
        $staff = Auth::user()->employee;
        $staffDepartmentId = optional($staff)->department_id;
        if (!$staffDepartmentId) {
            return redirect()->route('staff.dosen.index')->with('error', 'Data departemen tidak ditemukan.');
        }
        $dosen = Lecturer::where('id', $id)
            ->where('department_id', $staffDepartmentId)
            ->first();
        if (!$dosen) {
            return redirect()->route('staff.dosen.index')->with('error', 'Anda tidak memiliki akses mengubah data dosen ini.');
        }
        $validated = $request->validate([
            'nama_dosen' => 'required|string|max:255',
            'nidn' => 'required|string|max:20|unique:lecturers,nidn,' . $id,
            'email' => 'required|email|max:255|unique:lecturers,email,' . $id,
            'telp' => 'nullable|string|max:15',
            'tpl' => 'nullable|string|max:100',
            'tgl' => 'nullable|date',
            'gender' => 'required|in:Laki-Laki,Perempuan',
            'address' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $dosen->update($validated);

            DB::commit();
            return redirect()->route('staff.dosen.index')->with('update', 'Data Dosen berhasil diedit.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengupdate data dosen.');
        }
    }

    public function destroy($id)
    {
        $staff = Auth::user()->employee;

        if (!$staff || !$staff->department_id) {
            return redirect()->route('staff.dosen.index')->with('error', 'Data departemen tidak ditemukan.');
        }

        $dosen = Lecturer::where('id', $id)
            ->where('department_id', $staff->department_id)
            ->first();

        if (!$dosen) {
            return redirect()->route('staff.dosen.index')->with('error', 'Anda tidak memiliki akses menghapus dosen ini.');
        }

        $dosen->delete();
        return redirect()->route('staff.dosen.index')->with('success', 'Data Dosen berhasil dihapus.');
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        $file = $request->file('file');
        Excel::import(new DosenImportByStaff, $file);

        unlink($file->getRealPath());
        return redirect()->back()->with('success', 'Data Dosen berhasil diimport!');
    }

    public function assignUserToDosen(Request $request, $id)
    {
        $staff = auth()->user();
        $lecturer = Lecturer::findOrFail($id);

        if ($staff->employee && $lecturer->department_id !== $staff->employee->department_id) {
            return redirect()->back()->with('error', 'Anda hanya dapat mengaktifkan akun Dosen dari department Anda.');
        }

        if ($lecturer->user_id) {
            return redirect()->back()->with('error', 'Dosen ini sudah memiliki akun aktif.');
        }

        try {
            DB::beginTransaction();
            $user = new User();
            $user->name = $lecturer->nama_dosen;
            $user->email = $lecturer->email;
            $user->password = Hash::make($lecturer->nidn);
            $user->save();

            $user->roles()->attach(5);

            $lecturer->user_id = $user->id;
            $lecturer->save();

            DB::commit();
            Log::info('Transaction committed successfully for Lecturer ID: ' . $id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error assigning user to Lecturer ID: ' . $id . ', Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengaktifkan akun: ' . $e->getMessage());
        }
        return redirect()->route('staff.dosen.index')->with('success', 'Akun Dosen berhasil diaktifkan!');
    }

    public function unassignRoleToDosen($id)
    {
        $staff = auth()->user();
        $dosen = Lecturer::findOrFail($id);
        if ($staff->lecturer && $dosen->department_id !== $staff->lecturer->department_id) {
            return redirect()->back()->with('error', 'Anda hanya dapat menonaktifkan akun dosen dari prodi Anda.');
        }
        if ($dosen->user_id) {
            $user = User::find($dosen->user_id);

            if ($user) {
                $user->roles()->detach(Role::where('name', 'dosen')->first());
                $dosen->user_id = null;
                $dosen->save();
                $user->delete();
            }
        }

        return redirect()->route('staff.dosen.index')->with('success', 'Dosen berhasil di-unassign sebagai user.');
    }

}