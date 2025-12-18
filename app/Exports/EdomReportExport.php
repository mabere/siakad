<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EdomReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $lecturerAverages;
    protected $academicYear;

    public function __construct($lecturerAverages, $academicYear)
    {
        $this->lecturerAverages = $lecturerAverages;
        $this->academicYear = $academicYear;
    }

    public function collection()
    {
        return $this->lecturerAverages;
    }

    public function headings(): array
    {
        return [
            'Nama Dosen',
            'Program Studi',
            'Jumlah MK',
            'Jumlah Responden',
            'Rata-rata Nilai',
            'Tahun Akademik'
        ];
    }

    public function map($row): array
    {
        return [
            $row->nama_dosen,
            $row->department_name ?? 'N/A',
            $row->course_count,
            $row->student_count,
            number_format($row->average, 2),
            $this->academicYear->ta . ' ' . $this->academicYear->semester
        ];
    }
}