<?php

namespace App\Imports;

use App\Models\Publication;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LecturerPublicationImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $publication = new Publication([
            'lecturer_id' => $row['lecturer_id'],
            'title' => $row['title'],
            'abstract' => $row['abstract'],
            'media' => $row['media'],
            'media_name' => $row['media_name'],
            'issue' => $row['issue'],
            'year' => $row['year'],
            'page' => $row['page'],
            'citation' => 1,
        ]);

        $publication->save();

        // Simpan data publikasi ke tabel pivot lecturer_publication
        $publication->lecturers()->attach($row['lecturer_id']);

        return $publication;

    }
}