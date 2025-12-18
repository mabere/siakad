<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;

class KelasService
{
    public function getKelasForUser($user)
    {
        if ($user->hasRole('admin')) {
            return Kelas::with('department')->get();
        }
        return Kelas::where('department_id', $user->employee->department_id)->with('department')->get();
    }

    public function getCreateData($user)
    {
        $data = [
            'dosen' => Lecturer::where('department_id', $user->hasRole('admin') ? null : $user->employee->department_id)->get(),
        ];
        if ($user->hasRole('admin')) {
            $data['prodi'] = Department::all();
        }
        return $data;
    }

    public function createKelas(array $data, $user)
    {
        $data['department_id'] = $user->hasRole('admin') ? $data['department_id'] : $user->employee->department_id;
        return Kelas::create($data);
    }

    public function getShowData(Kelas $kelas)
    {
        $mahasiswa = Student::where('kelas_id', $kelas->id)->with('department')->get();
        return [
            'item' => $kelas->load(['department', 'lecturer']),
            'mahasiswa' => $mahasiswa,
            'jumlahMahasiswa' => $mahasiswa->count(),
            'idKelas' => $kelas->id,
        ];
    }

    public function getEditData(Kelas $kelas, $user)
    {
        return [
            'kelas' => $kelas,
            'dosen' => Lecturer::where('department_id', $user->hasRole('admin') ? null : $user->employee->department_id)->get(),
            'prodi' => $user->hasRole('admin') ? Department::all() : collect([]),
        ];
    }

    public function updateKelas(Kelas $kelas, array $data)
    {
        $kelas->update($data);
        return $kelas;
    }

    public function deleteKelas(Kelas $kelas)
    {
        if ($kelas->students()->count() > 0) {
            throw new \Exception('Kelas tidak bisa dihapus karena masih ada mahasiswa terkait.');
        }
        $kelas->delete();
    }

    public function getAddStudentsData(Kelas $kelas)
    {
        $mahasiswa = Student::whereNull('kelas_id')
            ->where('department_id', $kelas->department_id)
            ->with('department')
            ->get();
        return [
            'kelas' => $kelas,
            'mahasiswa' => $mahasiswa,
        ];
    }

    public function addStudentsToKelas(Kelas $kelas, array $studentIds)
    {
        Student::whereIn('id', $studentIds)
            ->where('department_id', $kelas->department_id)
            ->update(['kelas_id' => $kelas->id]);
    }

    public function removeStudentFromKelas(Kelas $kelas, $studentId)
    {
        $student = Student::findOrFail($studentId);
        if ($student->kelas_id != $kelas->id) {
            throw new \Exception('Mahasiswa tidak terdaftar di kelas ini.');
        }
        $student->update(['kelas_id' => null]);
    }
}
