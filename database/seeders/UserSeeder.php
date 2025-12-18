<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Faculty;
use App\Models\Student;
use App\Models\Building;
use App\Models\Employee;
use App\Models\Lecturer;
use App\Models\Department;
use App\Models\AcademicYear;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // $admin = User::create([
        //     'name' => 'Admin',
        //     'email' => 'admin@gmail.com',
        //     'password' => bcrypt('12345678'),
        // ]);

        // // Cari role admin
        // $adminRole = Role::where('slug', 'admin')->first();

        // // Attach role ke user
        // $admin->roles()->attach($adminRole->id);

        // $building = Building::create([
        //     'nama' => 'Gedung Rektorat',
        //     'lokasi' => 'Depan',
        // ]);
        // $building = Building::create([
        //     'nama' => 'Gedung A',
        //     'lokasi' => 'Timur',
        // ]);
        // $building = Building::create([
        //     'nama' => 'Gedung B',
        //     'lokasi' => 'Timur',
        // ]);
        // $building = Building::create([
        //     'nama' => 'Gedung C',
        //     'lokasi' => 'Barat',
        // ]);
        // $building = Building::create([
        //     'nama' => 'Gedung D',
        //     'lokasi' => 'Barat',
        // ]);

        // $room = Room::create([
        //     'name' => 'Ruangan A',
        //     'building_id' => '2',
        //     'nomor' => 'A1',
        // ]);
        // $room = Room::create([
        //     'name' => 'Ruangan B',
        //     'building_id' => '3',
        //     'nomor' => 'B1',
        // ]);
        // $room = Room::create([
        //     'name' => 'Ruangan C',
        //     'building_id' => '4',
        //     'nomor' => 'C1',
        // ]);
        // $room = Room::create([
        //     'name' => 'Ruangan D',
        //     'building_id' => '4',
        //     'nomor' => 'D1',
        // ]);

        // $faculty = Faculty::create([
        //     'nama' => 'Fakultas Keguruan dan Ilmu Pendidikan',
        //     'dekan' => 'Dr. Anas, S.Ag., M.Pd.',
        //     'nip' => '0912027502',
        //     'visi' => 'Visi',
        //     'misi' => 'Misi',
        // ]);
        // $department = Department::create([
        //     'faculty_id' => '1',
        //     'nama' => 'Pendidikan Bahasa dan Sastra Indonesia',
        //     'kaprodi' => 'Alan, S.Pd., M.Pd.',
        //     'nip' => '091501001',
        //     'visi' => 'Visi',
        //     'misi' => 'Misi',
        // ]);
        // $department = Department::create([
        //     'faculty_id' => '1',
        //     'nama' => 'Pendidikan Bahasa Inggris',
        //     'kaprodi' => 'Sukmawati, S.Pd., M.Pd.',
        //     'nip' => '091501003',
        //     'visi' => 'Visi',
        //     'misi' => 'Misi',
        // ]);
        // $department = Department::create([
        //     'faculty_id' => '1',
        //     'nama' => 'Pendidikan Matematika',
        //     'kaprodi' => 'Salmawati, S.Pd., M.Pd.',
        //     'nip' => '091501002',
        //     'visi' => 'Visi',
        //     'misi' => 'Misi',
        // ]);
        // $lecturer = Lecturer::create([
        //     'faculty_id' => '1',
        //     'department_id' => '1',
        //     'nama_dosen' => 'Dr. Anas, S.Ag., M.Pd.',
        //     'nidn' => '0912027502',
        //     'address' => 'Jl. Sultan Hasanuddin No. 129, Lalosabila',
        //     'telp' => '085212045001',
        //     'email' => 'anas@gmail.com',
        //     'gender' => 'Laki-Laki',
        //     'tpl' => 'Unaaha',
        //     'tgl' => '1982-01-01',
        // ]);
        // $lecturer = Lecturer::create([
        //     'faculty_id' => '1',
        //     'department_id' => '1',
        //     'nama_dosen' => 'Alan, S.Pd., M.Pd.',
        //     'nidn' => '0904078601',
        //     'address' => 'Jl. Kemandirian Teguh Buana, No. 111, Pangka',
        //     'telp' => '085232012001',
        //     'email' => 'alan@gmail.com',
        //     'gender' => 'Laki-Laki',
        //     'tpl' => 'Kota Kembara Lamu',
        //     'tgl' => '1986-01-01',
        // ]);
        // $lecturer = Lecturer::create([
        //     'faculty_id' => '1',
        //     'department_id' => '2',
        //     'nama_dosen' => 'Sukmawati, S.Pd., M.Pd.',
        //     'nidn' => '0901018401',
        //     'address' => 'Jl. Sultan Hasanuddin No. 129, Lalosabila',
        //     'telp' => '085212045001',
        //     'email' => 'sukmawati@gmail.com',
        //     'gender' => 'Perempuan',
        //     'tpl' => 'Unaaha',
        //     'tgl' => '1984-01-01',
        // ]);
        // $lecturer = Lecturer::create([
        //     'faculty_id' => '1',
        //     'department_id' => '3',
        //     'nama_dosen' => 'Salmawati, S.Pd., M.Pd.',
        //     'nidn' => '0901018201',
        //     'address' => 'Jl. Sultan Hasanuddin No. 129, Lalosabila',
        //     'telp' => '085212045001',
        //     'email' => 'salmawati@gmail.com',
        //     'gender' => 'Perempuan',
        //     'tpl' => 'Unaaha',
        //     'tgl' => '1982-01-01',
        // ]);

        $kelas = Kelas::create([
            'department_id' => '2',
            'lecturer_id' => '2',
            'name' => 'PBI',
            'angkatan' => '2021',
            'total' => '20',
        ]);
        $kelas = Kelas::create([
            'department_id' => '2',
            'lecturer_id' => '2',
            'name' => 'PBI',
            'angkatan' => '2022',
            'total' => '20',
        ]);
        $kelas = Kelas::create([
            'department_id' => '2',
            'lecturer_id' => '2',
            'name' => 'PBI',
            'angkatan' => '2023',
            'total' => '20',
        ]);
        $kelas = Kelas::create([
            'department_id' => '2',
            'lecturer_id' => '2',
            'name' => 'PBI',
            'angkatan' => '2024',
            'total' => '20',
        ]);
        $kelas = Kelas::create([
            'department_id' => '2',
            'lecturer_id' => '2',
            'name' => 'PBI',
            'angkatan' => '2025',
            'total' => '20',
        ]);

        // $student = Student::create([
        //     'department_id' => '2',
        //     'nama_mhs' => 'Budi',
        //     'nim' => '220501001',
        //     'entry_year' => 2020,
        //     'entry_semester' => 1,
        //     'address' => 'Jl. Sultan Hasanuddin No. 129, Lalosabila',
        //     'telp' => '0852120001',
        //     'email' => 'budi@gmail.com',
        //     'gender' => 'Laki-Laki',
        //     'tpl' => 'Unaaha',
        //     'tgl' => '2005-01-01',
        //     'total_sks' => 0,
        // ]);

        // $academicYear = AcademicYear::create([
        //     'ta' => '2024-2025',
        //     'semester' => 'Ganjil',
        //     'status' => '0',
        //     'start_date' => '2024-09-01',
        //     'end_date' => '2025-02-28',
        //     'krs_open_date' => '2024-07-01',
        //     'krs_close_date' => '2024-09-20',
        // ]);
        $academicYear = AcademicYear::create([
            'ta' => '2024-2025',
            'semester' => 'Genap',
            'status' => '1',
            'start_date' => '2025-03-01',
            'end_date' => '2025-07-31',
            'krs_open_date' => '2025-02-01',
            'krs_close_date' => '2025-03-30',
        ]);

        // $staff = Employee::create([
        //     'department_id' => 1,
        //     'nama' => 'Feeder PBSI',
        //     'email' => 'feeder.pbsi@gmail.com',
        //     'position' => 'Feeder',
        //     'gender' => 'Laki-Laki',
        //     'nip' => '091503001',
        //     'tpl' => 'Unaah',
        //     'tgl' => '1985-01-01',
        //     'address' => 'Unaaha'
        // ]);
        // $staff = Employee::create([
        //     'department_id' => 2,
        //     'nama' => 'Feeder PBI',
        //     'email' => 'feeder.pbi@gmail.com',
        //     'position' => 'Feeder',
        //     'gender' => 'Laki-Laki',
        //     'nip' => '091502001',
        //     'tpl' => 'Unaah',
        //     'tgl' => '1985-01-01',
        //     'address' => 'Unaaha'
        // ]);
        // $staff = Employee::create([
        //     'department_id' => 3,
        //     'nama' => 'Feeder PBSI',
        //     'email' => 'feeder.mtk@gmail.com',
        //     'position' => 'Feeder',
        //     'gender' => 'Laki-Laki',
        //     'nip' => '091501001',
        //     'tpl' => 'Unaah',
        //     'tgl' => '1985-01-01',
        //     'address' => 'Unaaha'
        // ]);
        // $staff = Employee::create([
        //     'department_id' => null,
        //     'nama' => 'KTU FKIP',
        //     'email' => 'ktu@gmail.com',
        //     'position' => 'KTU',
        //     'gender' => 'Perempuan',
        //     'nip' => '091500001',
        //     'tpl' => 'Unaah',
        //     'tgl' => '1986-01-01',
        //     'address' => 'Unaaha'
        // ]);

    }
}