<?php

namespace App\Imports;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CourseImportByStaff implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // dd($row);

        return new Course([
            'department_id' => Auth::user()->employee->department_id,
            'name' => $row['name'],
            'code' => $row['code'],
            'sks' => $row['sks'],
            'smt' => $row['smt'],
            'semester' => $row['semester'],
            'kategori' => $row['kategori'],
        ]);
    }
}
