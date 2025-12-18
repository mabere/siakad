<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PenunjangExport implements FromCollection, WithHeadings, WithMapping
{
    protected $penunjangs;

    public function __construct($penunjangs)
    {
        $this->penunjangs = $penunjangs;
    }

    public function collection()
    {
        return $this->penunjangs;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Dosen',
            'Department',
            'Judul Kegiatan',
            'Level',
            'Penyelenggara',
            'Status',
            'Tanggal Validasi'
        ];
    }

    public function map($penunjang): array
    {
        return [
            $penunjang->date->format('d/m/Y'),
            $penunjang->lecturer->nama_dosen,
            $penunjang->lecturer->department->nama,
            $penunjang->title,
            $penunjang->level,
            $penunjang->organizer,
            $penunjang->status,
            $penunjang->validated_at ? $penunjang->validated_at->format('d/m/Y') : '-'
        ];
    }
}
