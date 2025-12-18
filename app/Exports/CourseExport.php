<?php

// namespace App\Exports;

// use App\Models\Course;
// use Maatwebsite\Excel\Concerns\WithHeadings;
// use Maatwebsite\Excel\Concerns\FromCollection;

// class CourseExport implements FromCollection, WithHeadings
// {
//     public function collection()
//     {
//         return Course::all();
//     }

//     public function headings(): array
//     {
//         return [
//             "id",
//             "department_id",
//             "mk",
//             "code",
//             "sks",
//             "smt",
//             "semester",
//             "kategori"
//         ];
//     }
// }



namespace App\Exports;

use App\Models\Course;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CourseExport implements FromCollection, WithHeadings
{
    protected $curriculumId;

    public function __construct($curriculumId)
    {
        $this->curriculumId = $curriculumId;
    }

    public function collection()
    {
        return Course::where('curriculum_id', $this->curriculumId)->get([
            'name',
            'code',
            'sks',
            'semester_number',
            'kategori',
            'prerequisites'
        ]);
    }

    public function headings(): array
    {
        return [
            'Nama Mata Kuliah',
            'Kode',
            'SKS',
            'Semester',
            'Kategori',
            'Prasyarat',
        ];
    }
}
