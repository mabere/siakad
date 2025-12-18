<?php

namespace App\Http\Controllers\Dekan;

use Illuminate\Http\Request;
use App\Models\LetterRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Traits\ProcessesLetterRequest;
use Illuminate\Support\Facades\Storage;

class LetterRequestByDekanController extends Controller
{

    use ProcessesLetterRequest;

    public function index()
    {
        $user = Auth::user();

        // Ambil fakultas dekan melalui department
        $fakultasId = $user->lecturer->department->faculty_id;

        // Ambil hanya surat yang diajukan oleh mahasiswa dari fakultas yang sama
        $ajuanSurat = LetterRequest::whereHas('user', function ($query) use ($fakultasId) {
            $query->whereHas('student', function ($q) use ($fakultasId) {
                $q->whereHas('department', function ($d) use ($fakultasId) {
                    $d->where('faculty_id', $fakultasId);
                });
            });
        })->with('user', 'user.student.department', 'letterType')->latest()->paginate(10);

        return view('dekan.surat-masuk.index', compact('ajuanSurat'));
    }

    public function show($id)
    {
        $letterRequest = LetterRequest::with('user', 'letterType')->findOrFail($id);
        return view('dekan.surat-masuk.show', compact('letterRequest'));
    }

    public function update(Request $request, $id)
    {
        $letterRequest = LetterRequest::findOrFail($id);

        return $this->process($request, $letterRequest);
    }
}