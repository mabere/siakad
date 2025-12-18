<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lecturer;
use App\Models\Addition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PenunjangAdminController extends Controller
{
    public function index(Request $request)
    {
        $dosen = $request->session()->get('dosen');
        $penunjang = Addition::with('lecturer')->get();
        return view(request()->routeIs('admin.*') ? 'backend.penunjang.index' : 'dosen.tridharma.penunjang.index', compact('penunjang'));
    }

    public function show($id)
    {
        $penunjang = Addition::findOrFail($id);
        return view(request()->routeIs('admin.*') ? 'backend.penunjang.show' : 'dosen.tridharma.penunjang.show', compact('penunjang'));
    }

    public function edit($id)
    {
        $penunjang = Addition::findOrFail($id);
        $lecturers = Lecturer::all();
        return view(request()->routeIs('admin.*') ? 'backend.penunjang.edit' : 'dosen.tridharma.penunjang.edit', compact('penunjang', 'lecturers'));
    }

    public function update(Request $request, $id)
    {
        $penunjang = Addition::findOrFail($id);
        Gate::authorize('update', $penunjang);

        $validatedData = $request->validate([
            'lecturer_id' => 'required|exists:lecturers,id',
            'title' => 'required|string|max:255',
            'organizer' => 'required|string|max:255',
            'level' => 'required|in:Nasional,Internasional,Regional',
            'peran' => 'required|string|max:255',
            'date' => 'required|date',
            'proof' => 'nullable|file|mimes:pdf,doc,docx|max:2048'
        ]);

        if ($request->hasFile('proof')) {
            $file = $request->file('proof');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/penunjang'), $filename);
            $validatedData['proof'] = $filename;
        }

        $penunjang->update($validatedData);

        return redirect()
            ->route(request()->routeIs('admin.*') ? 'admin.penunjang.index' : 'lecturer.penunjang.index')
            ->with('success', 'Data Penunjang berhasil diperbarui!');
    }

    public function create()
    {
        $dosen = Lecturer::all();
        return view('dosen.tridharma.penunjang.create', compact('dosen'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'lecturer_id' => 'required|exists:lecturers,id',
            'title' => 'required|string|max:255',
            'organizer' => 'required|string|max:255',
            'level' => 'required|in:Nasional,Internasional,Regional',
            'peran' => 'required|string|max:255',
            'date' => 'required|date',
            'proof' => 'required|file|mimes:pdf,doc,docx|max:2048'
        ]);

        if ($request->hasFile('proof')) {
            $file = $request->file('proof');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/penunjang'), $filename);
            $validatedData['proof'] = $filename;
        }

        Addition::create($validatedData);

        return redirect()
            ->route(request()->routeIs('admin.*') ? 'admin.penunjang.index' : 'lecturer.penunjang.index')
            ->with('success', 'Data Penunjang berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $penunjang = Addition::findOrFail($id);
        Gate::authorize('delete', $penunjang);

        // Delete the proof file if it exists
        if ($penunjang->proof && file_exists(public_path('uploads/penunjang/' . $penunjang->proof))) {
            unlink(public_path('uploads/penunjang/' . $penunjang->proof));
        }

        $penunjang->delete();

        return redirect()
            ->route(request()->routeIs('admin.*') ? 'admin.penunjang.index' : 'lecturer.penunjang.index')
            ->with('success', 'Data Penunjang berhasil dihapus!');
    }
}