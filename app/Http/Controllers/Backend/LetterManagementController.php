<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\LetterRequest;
use App\Models\LetterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Traits\ProcessesLetterRequest;

class LetterManagementController extends Controller
{
    use ProcessesLetterRequest;

    public function index()
    {
        $requests = LetterRequest::with(['user', 'letterType'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Count submitted requests for badge
        $pendingCount = LetterRequest::where('status', 'submitted')->count();

        return view('admin.letters.index', compact('requests', 'pendingCount'));
    }

    public function indexMhs()
    {
        $requests = LetterRequest::with(['user', 'letterType'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Count submitted requests for badge
        $pendingCount = LetterRequest::where('status', 'submitted')->count();

        return view('admin.letters.index', compact('requests', 'pendingCount'));
    }

    public function show(LetterRequest $letterRequest)
    {
        return view('admin.letters.show', compact('letterRequest'));
    }

    public function showMhs(LetterRequest $letterRequest)
    {
        return view('admin.letters.show-mhs', compact('letterRequest'));
    }

}