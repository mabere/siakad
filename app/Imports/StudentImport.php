<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // dd($row);
        return new Student([
            'department_id' => !empty($row['department_id']) ? $row['department_id'] : 2,
            'kelas_id' => !empty($row['kelas_id']) ? $row['kelas_id'] : 2,
            'nama_mhs' => $row['nama_mhs'],
            'nim' => $row['nim'],
            'address' => $row['address'],
            'telp' => $row['telp'],
            'email' => $row['email'],
            'gender' => $row['gender'],
            'tpl' => $row['tpl'],
            'tgl' => $row['tgl'],
        ]);
    }
}