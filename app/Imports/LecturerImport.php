<?php

namespace App\Imports;

use App\Models\Lecturer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LecturerImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Lecturer([
            'faculty_id' => !empty($row['faculty_id']) ? $row['faculty_id'] : 1,
            'department_id' => !empty($row['department_id']) ? $row['department_id'] : 2,
            'nama_dosen' => $row['nama_dosen'],
            'nidn' => $row['nidn'],
            'address' => $row['address'],
            'telp' => $row['telp'],
            'email' => $row['email'],
            'gender' => $row['gender'],
            'tpl' => $row['tpl'],
            'tgl' => $row['tgl'],
        ]);
    }
}