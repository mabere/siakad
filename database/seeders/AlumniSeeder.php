<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alumni;
use App\Models\Student;

class AlumniSeeder extends Seeder
{
    public function run()
    {
        $students = Student::whereHas('studentSemesterStatus', function ($query) {
            $query->where('status', 'lulus');
        })->get();

        foreach ($students as $student) {
            Alumni::create([
                'student_id' => $student->id,
                'graduation_year' => now()->year - rand(1, 5), // Tahun lulus acak
                'job_title' => 'Software Engineer', // Contoh pekerjaan
                'company' => 'Tech Corp', // Contoh perusahaan
                'industry' => 'Technology', // Contoh sektor
                'salary_range' => '5-10 juta', // Contoh rentang gaji
                'further_education' => 'S2 di Universitas X', // Contoh pendidikan lanjutan
                'contribution' => 'Donasi 1 juta', // Contoh kontribusi
                'status' => 'aktif',
                'visibility' => 'internal',
            ]);
        }
    }
}