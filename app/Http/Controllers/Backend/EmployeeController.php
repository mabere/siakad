<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $pegawai = Employee::with('department')->get();
        return view('backend.pegawai.index', compact('pegawai'));
    }

    public function create()
    {
        $department = Department::all();
        $pegawai = Employee::all();
        return view('backend.pegawai.create', compact('department', 'pegawai'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
            'nip' => 'required|string',
            'email' => 'required|email|unique:employees,email',
            'position' => 'required|string',
        ]);

        // Ambil user yang sedang login (misalnya admin)
        $adminUser = Auth::user();
        if (!$adminUser) {
            abort(403, 'Unauthorized action.');
        }

        // Gunakan transaksi agar semua operasi berjalan konsisten
        DB::transaction(function () use ($request, $validated) {
            // 1. Buat data Employee terlebih dahulu (tanpa user_id)
            $employee = new Employee();
            $employee = new Employee;
            $employee->nama = $request->nama;
            $employee->department_id = $request->department_id;
            $employee->nip = $request->nip;
            $employee->email = $request->email;
            $employee->position = $request->position;
            $employee->save();

            // 2. Buat data User berdasarkan data Employee
            $newUser = new User();
            $newUser->name = $employee->nama;
            $newUser->email = $employee->email;
            $newUser->password = Hash::make($employee->nip);
            $newUser->save();

            // 3. Update Employee dengan user_id yang baru dibuat
            $employee->user_id = $newUser->id;
            $employee->save();

            // 4. Assign role employee (misalnya role_id = 7) ke pivot role_user
            $newUser->roles()->attach(7);
        });

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Data Pegawai berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $departments = Department::all();
        return view('backend.pegawai.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id); // Ambil data Employee yang akan diupdate

        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
            'nip' => 'required|string',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'position' => 'required|string',
        ]);

        // Gunakan transaksi agar update pada Employee dan User terkait konsisten
        DB::transaction(function () use ($request, $employee, $validated) {
            // Update data Employee (tanpa mengubah user_id)
            $employee->nama = $validated['nama'];
            $employee->department_id = $validated['department_id'];
            $employee->nip = $validated['nip'];
            $employee->email = $validated['email'];
            $employee->position = $validated['position'];
            $employee->save();

            // Update data User terkait (jika Employee sudah terhubung dengan User)
            if ($employee->user) {
                $employee->user->name = $employee->nama;
                $employee->user->email = $employee->email;
                // Jika perlu update password berdasarkan nip yang baru, uncomment baris berikut:
                // $employee->user->password = Hash::make($employee->nip);
                $employee->user->save();
            }
        });

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Data Pegawai berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);

        DB::transaction(function () use ($employee) {
            $user = $employee->user;
            // Hapus file foto jika ada
            if ($employee->photo && Storage::exists('public/images/staff/' . $user->photo)) {
                Storage::delete('public/images/staff/' . $user->photo);
            }

            // Hapus user terkait jika ada
            if ($employee->user) {
                $employee->user->roles()->detach(); // Cabut role dari pivot role_user
                $employee->user->delete(); // Hapus record user
            }
            $employee->delete(); // Hapus data employee
        });

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Data Pegawai berhasil dihapus!');
    }

    public function show($id)
    {
        $pegawai = Employee::with('department')->findOrFail($id);
        return view('backend.pegawai.show', compact('pegawai'));
    }

}