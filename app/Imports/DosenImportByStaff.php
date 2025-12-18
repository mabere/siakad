<?php

namespace App\Imports;

use App\Models\Lecturer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DosenImportByStaff implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Lecturer([
            'department_id' => $row['department_id'],
            'nama_dosen' => $row['nama_dosen'],
            'nidn' => $row['nidn'],
            'address' => $row['address'] ?? 'Unaaha',
            'telp' => $row['telp'],
            'email' => $row['email'],
            'gender' => $row['gender'] ?? 'Laki-Laki',
            'tpl' => $row['tpl'] ?? 'Unaaha',
            'tgl' => $row['tgl'],
            'photo' => $row['photo'] ?? 'dosen.jpg',
        ]);
    }
}