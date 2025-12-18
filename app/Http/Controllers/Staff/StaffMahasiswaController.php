<?php

namespace App\Http\Controllers\Staff;

use App\Models\Role;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Student;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentImportByStaff;
use Illuminate\Support\Facades\Storage;

class StaffMahasiswaController extends Controller
{
    // app/Http/Controllers/MahasiswaController.php (sesuaikan dengan nama controller Anda)

    public function index()
    {
        $user = Auth::user();
        if (!$user->employee) {
            return redirect()->route('dashboard')->with('error', 'Data employee tidak ditemukan.');
        }
        $mhs = collect();
        if ($user->employee->level === 'faculty') {
            if (!$user->employee->faculty_id) {
                return redirect()->route('dashboard')->with('error', 'Data fakultas KTU tidak ditemukan.');
            }
            $mhs = Student::where('faculty_id', $user->employee->faculty_id)
                ->with(['kelas', 'advisor'])
                ->get();
        } elseif ($user->employee->level === 'department') {
            if (!$user->employee->department_id) {
                return redirect()->route('dashboard')->with('error', 'Data departemen tidak ditemukan.');
            }
            $mhs = Student::where('department_id', $user->employee->department_id)
                ->with(['kelas', 'advisor'])
                ->get();
        } else {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses.');
        }
        return view('staff.mhs.index', compact('mhs'));
    }

    public function showDetail($id)
    {
        $user = Auth::user();
        if (!$user->employee) {
            return redirect()->back()->with('error', 'Data employee tidak ditemukan.');
        }
        $student = null;
        $hasAccess = false;
        if ($user->employee->level === 'faculty') {
            if ($user->employee->faculty_id) {
                $student = Student::with(['department', 'advisor'])
                    ->where('id', $id)
                    ->where('faculty_id', $user->employee->faculty_id)
                    ->first();
                if ($student) {
                    $hasAccess = true;
                }
            }
        } elseif ($user->employee->level === 'department') {
            if ($user->employee->department_id) {
                $student = Student::with(['department', 'advisor'])
                    ->where('id', $id)
                    ->where('department_id', $user->employee->department_id)
                    ->first();
                if ($student) {
                    $hasAccess = true;
                }
            }
        }
        if (!$hasAccess) {
            return redirect()->route('staff.mahasiswa.index')
                ->with('error', 'Mahasiswa tidak ditemukan atau Anda tidak memiliki akses.');
        }
        return view('staff.mhs.show', compact('student'));
    }

    public function create()
    {
        $staff = Auth::user()->employee;
        $kelas = Kelas::where('department_id', $staff->department_id)->get();
        return view('staff.mhs.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $staff = Auth::user()->employee;

        if (!$staff || !$staff->department) {
            return redirect()->route('staff.mahasiswa.index')->with('error', 'Data departemen tidak ditemukan.');
        }

        $request->validate([
            'nama_mhs' => 'required|string',
            'nim' => 'required|unique:students,nim',
            'email' => 'required|email',
            'address' => 'required',
            'telp' => 'required',
            'tpl' => 'required',
            'tgl' => 'required|date',
            'kelas_id' => 'nullable|exists:kelas,id',
            'gender' => 'required|in:Laki-Laki,Perempuan',
        ]);

        $data = new Student;
        $data->kelas_id = $request->kelas_id;
        $data->department_id = $staff->department_id;
        $data->nama_mhs = $request->nama_mhs;
        $data->nim = $request->nim;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->telp = $request->telp;
        $data->tpl = $request->tpl;
        $data->tgl = $request->tgl;
        $data->gender = $request->gender;
        $data->save();

        return redirect()->route('staff.mahasiswa.index')->with('success', 'Data Mahasiswa berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $staff = Auth::user()->employee;
        if (!$staff || !$staff->department_id) {
            return redirect()->back()->with('error', 'Data staff tidak valid.');
        }
        $mhs = Student::where('id', $id)
            ->where('department_id', $staff->department_id)
            ->first();
        if (!$mhs) {
            return redirect()->route('staff.mahasiswa.index')
                ->with('error', 'Mahasiswa tidak ditemukan atau Anda tidak memiliki akses untuk mengedit.');
        }
        $kelas = Kelas::where('department_id', $staff->department_id)->get();
        return view('staff.mhs.edit', compact('kelas', 'mhs'));
    }

    public function show()
    {
        $staff = Auth::user()->employee;
        $students = Student::where('department_id', $staff->department_id)
            ->whereNull('advisor_id')
            ->get();
        $lecturers = Lecturer::where('department_id', $staff->department_id)->get();

        return view('staff.mhs.advisor', compact('students', 'lecturers'));
    }

    public function assignAdvisor(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'advisor_id' => 'required|exists:lecturers,id',
        ]);

        Student::whereIn('id', $request->student_ids)
            ->update(['advisor_id' => $request->advisor_id]);

        return redirect()->route('staff.mahasiswa.advisor')
            ->with('success', 'Dosen PA berhasil diassign ke mahasiswa terpilih.');
    }

    public function update(Request $request, $id)
    {
        $staff = Auth::user()->employee;

        if (!$staff || !$staff->department) {
            return redirect()->route('staff.mahasiswa.index')->with('error', 'Data departemen tidak ditemukan.');
        }

        $mhs = Student::where('id', $id)
            ->where('department_id', $staff->department_id)
            ->firstOrFail();

        $request->validate([
            'nama_mhs' => 'required|string',
            'nim' => 'required|unique:students,nim,' . $id,
            'email' => 'required',
            'address' => 'required',
            'telp' => 'required',
            'tpl' => 'required',
            'tgl' => 'required',
        ]);

        $mhs->kelas_id = $request->kelas_id;
        $mhs->department_id = $staff->department_id;
        $mhs->nama_mhs = $request->nama_mhs;
        $mhs->nim = $request->nim;
        $mhs->email = $request->email;
        $mhs->address = $request->address;
        $mhs->telp = $request->telp;
        $mhs->tpl = $request->tpl;
        $mhs->tgl = $request->tgl;
        $mhs->save();

        return redirect()->route('staff.mahasiswa.index')->with('success', 'Data Mahasiswa berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $staff = Auth::user()->employee;
        if (!$staff || !$staff->department_id) {
            return redirect()->back()->with('error', 'Data staff tidak valid.');
        }
        $mhs = Student::where('id', $id)->where('department_id', $staff->department_id)->first();
        if (!$mhs) {
            return redirect()->route('staff.mahasiswa.index')
                ->with('error', 'Mahasiswa tidak ditemukan atau Anda tidak memiliki akses untuk menghapus.');
        }
        if ($mhs->photo) {
            Storage::delete('public/images/mhs/' . $mhs->photo);
        }
        if ($mhs->user) {
            $mhs->user->delete();
        }
        $mhs->delete();
        return redirect()->route('staff.mahasiswa.index')->with('success', 'Data Mahasiswa berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls',
        ]);

        $file = $request->file('file');
        Excel::import(new StudentImportByStaff, $file);

        // Hapus file setelah import
        unlink($file->getRealPath());

        // Excel::import(new StudentImportByStaff, $request->file('file'));

        return back()->with('success', 'Data mahasiswa berhasil diimport!');
    }

    public function assignUser(Student $mhs)
    {
        $user = new User();
        $user->name = $mhs->nama_mhs;
        $user->email = $mhs->email;
        $user->password = Hash::make($mhs->nim);
        $user->save();

        // Beri Role Mahasiswa
        $user->roles()->attach(6);

        // Hubungkan user dengan data mahasiswa
        $mhs->user_id = $user->id;
        $mhs->save();

        return redirect()->back()->with('success', 'Akun Mahasiswa berhasil dibuat/diaktifkan.');
    }

    public function assignUserToMahasiswa(Request $request, $id)
    {
        $staff = auth()->user();
        $student = Student::findOrFail($id);

        if ($staff->employee && $student->department_id !== $staff->employee->department_id) {
            return redirect()->back()->with('error', 'Anda hanya dapat mengaktifkan akun mahasiswa dari department Anda.');
        }

        if ($student->user_id) {
            return redirect()->back()->with('error', 'Mahasiswa ini sudah memiliki akun aktif.');
        }

        try {
            DB::beginTransaction();
            $user = new User();
            $user->name = $student->nama_mhs;
            $user->email = $student->email;
            $user->password = Hash::make($student->nim);
            $user->save();

            $user->roles()->attach(6);

            $student->user_id = $user->id;
            $student->save();

            DB::commit();
            Log::info('Transaction committed successfully for student ID: ' . $id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error assigning user to student ID: ' . $id . ', Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengaktifkan akun: ' . $e->getMessage());
        }
        return redirect()->route('staff.mahasiswa.index')->with('success', 'Akun mahasiswa berhasil diaktifkan!');
    }

    public function assignMultipleUserFromMahasiswa(Request $request)
    {
        $request->validate([
            'selected_students' => 'required|array',
            'selected_students.*' => 'exists:students,id'
        ]);

        try {
            DB::beginTransaction();

            $staff = auth()->user();
            $staffDepartmentId = $staff->lecturer ? $staff->lecturer->department_id : null;

            foreach ($request->selected_students as $studentId) {
                $student = Student::findOrFail($studentId);

                // Cek apakah mahasiswa dalam department staff
                if ($staffDepartmentId && $student->department_id !== $staffDepartmentId) {
                    continue; // Lewati jika bukan department staff
                }

                // Skip if student already has user account
                if ($student->user_id) {
                    continue;
                }

                // Create user account
                $user = User::firstOrCreate(
                    ['email' => $student->email],
                    [
                        'name' => $student->nama_mhs,
                        'password' => Hash::make($student->nim), // Gunakan NIM sebagai password default
                    ]
                );

                // Assign student role
                $studentRole = Role::where('name', 'mahasiswa')->first();
                $user->roles()->sync([$studentRole->id]);

                // Link user to student
                $student->update(['user_id' => $user->id]);
            }

            DB::commit();
            return redirect()->route('staff.mahasiswa.index')->with('success', 'Akun mahasiswa berhasil dibuat untuk semua yang dipilih dalam department Anda.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal membuat akun: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function unassignUserToMahasiswa($id)
    {
        $staff = auth()->user();
        $student = Student::findOrFail($id);
        if ($staff->lecturer && $student->department_id !== $staff->lecturer->department_id) {
            return redirect()->back()->with('error', 'Anda hanya dapat menonaktifkan akun mahasiswa dari department Anda.');
        }
        if ($student->user_id) {
            $user = User::find($student->user_id);

            if ($user) {
                $user->roles()->detach(Role::where('name', 'mahasiswa')->first());
                $student->user_id = null;
                $student->save();
                $user->delete();
            }
        }

        return redirect()->route('staff.mahasiswa.index')->with('success', 'Mahasiswa berhasil di-unassign sebagai user.');
    }
}
