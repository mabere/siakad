<?php

namespace App\Exports;

use App\Models\Course;
use App\Models\Schedule;
use App\Models\MkduCourse;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;

class EdomExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;
    protected $departmentId;

    public function __construct(array $filters = [], $departmentId = null)
    {
        $this->filters = $filters;
        $this->departmentId = $departmentId;
    }

    public function query()
    {
        $query = Schedule::query();
        $query->where(function ($subQuery) {
            $subQuery->whereHasMorph('schedulable', [Course::class], function ($morphQuery) {
                $morphQuery->where('department_id', $this->departmentId);
            })->orWhere(function ($subQuery) {
                $subQuery->whereHasMorph('schedulable', [MkduCourse::class])
                    ->whereHas('studyPlans.student', function ($studentQuery) {
                        $studentQuery->where('department_id', $this->departmentId);
                    });
            });
        });
        $query->with([
            'schedulable' => fn($morphTo) => $morphTo->morphWith([
                Course::class,
                MkduCourse::class,
            ]),
            'lecturersInSchedule',
            'responses.question.categoryName',
        ]);
        return $query->filter($this->filters);
    }

    public function headings(): array
    {
        return [
            'Mata Kuliah',
            'Kode Mata Kuliah',
            'Dosen Pengampu',
            'Total Responden',
            'Skor Rata-rata'
        ];
    }

    public function map($schedule): array
    {
        return [
            'Mata Kuliah' => $schedule->schedulable->name ?? 'N/A',
            'Kode Mata Kuliah' => $schedule->schedulable->code ?? 'N/A',
            'Dosen' => $schedule->lecturersInSchedule->pluck('nama_dosen')->join(', '),
            'Total Respons' => $schedule->responses->count(),
            'Rata-rata' => number_format($schedule->responses->avg('rating') ?? 0, 2),
        ];
    }

}
