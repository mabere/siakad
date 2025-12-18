<?php

namespace App\Http\Controllers\Ktu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// app/Http/Controllers/Ktu/DocumentController.php

namespace App\Http\Controllers\Ktu;

use App\Http\Controllers\Controller;
use App\Models\ThesisDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class DocumentController extends Controller
{


    public function show(ThesisDocument $document)
    {
        // Eager loading untuk menghindari N+1 query problem
        $document->load(['thesis.student.department']);

        $user = auth()->user();

        // Periksa apakah relasi thesis dan student ada sebelum mengaksesnya
        $ktuFacultyId = $user->employee->faculty_id;
        $studentFacultyId = $document->thesis?->student?->department?->faculty_id;

        // Jika salah satu relasi tidak ada, atau fakultas tidak cocok
        if (!$studentFacultyId || $ktuFacultyId !== $studentFacultyId) {
            abort(403, 'Anda tidak memiliki akses untuk melihat dokumen ini.');
        }

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        $filePath = Storage::disk('local')->path($document->file_path);
        $fileName = basename($document->file_path);

        return response()->file($filePath, [
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }
}
