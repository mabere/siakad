<?php

namespace App\Exports;

use App\Models\Lecturer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LecturersExport implements FromCollection, WithHeadings
{
    protected $departmentId;

    public function __construct($departmentId)
    {
        $this->departmentId = $departmentId;
    }

    public function collection()
    {
        return Lecturer::where('department_id', $this->departmentId)
            ->select('nama_dosen', 'nidn', 'jafung', 'email', 'gender')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Dosen',
            'NIDN',
            'Jabatan Fungsional',
            'Email',
            'Gender',
        ];
    }
}