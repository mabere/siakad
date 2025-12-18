<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImportByStaff implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Student([
            'department_id' => $row['department_id'],
            'kelas_id' => $row['kelas_id'],
            'nama_mhs' => $row['nama_mhs'],
            'nim' => $row['nim'],
            'address' => $row['address'],
            'telp' => $row['telp'],
            'email' => $row['email'],
            'gender' => $row['gender'],
            'tpl' => $row['tpl'],
            'tgl' => $row['tgl']
        ]);
    }
}
