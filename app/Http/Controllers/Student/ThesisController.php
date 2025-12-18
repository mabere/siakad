<?php

namespace App\Http\Controllers\Student;

use App\Models\Thesis;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ThesisController extends Controller
{
    public function show(Thesis $thesis)
    {
        $this->authorize('view', $thesis);

        $thesis->load(['supervisions.supervisor', 'meetings', 'documents', 'exam.examiners']);

        return view('backend.skripsi.mhs.show', compact('thesis'));
    }

}