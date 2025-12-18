<?php

namespace App\Imports;

use App\Models\Publication;
use App\Models\Service;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LecturerPkmImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $pkm = new Service([
            'lecturer_id' => $row['lecturer_id'],
            'title' => $row['title'],
            'pendanaan' => $row['pendanaan'],
            'year' => $row['year'],
        ]);

        $pkm->save();

        // Simpan data Pengadian ke tabel pivot lecturer_service
        $pkm->lecturers()->attach($row['lecturer_id']);

        return $pkm;

    }
}
