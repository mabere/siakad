<?php

namespace App\Imports;

use App\Models\Addition;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LecturerPenunjangImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $penunjang = new Addition([
            'lecturer_id' => auth()->user()->lecturer->id,
            'title' => $row['title'],
            'peran' => $row['peran'],
            'proof' => $row['proof'],
            'date' => $row['date'],
            'organizer' => $row['organizer'],
            'level' => $row['level'],
        ]);

        $penunjang->save();
        return $penunjang;

    }
}