<?php

namespace App\Http\Controllers\Lecturer;

use App\Imports\LecturerPenunjangImport;
use App\Models\Lecturer;
use App\Models\Publication;
use Illuminate\Http\Request;
use App\Imports\LecturerPkmImport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LecturerPublicationImport;

class ResearchController extends Controller
{
    public function index(Request $request)
    {
        // Check if it's admin route
        if (request()->routeIs('admin.*')) {
            $publications = Publication::with('lecturers')
                ->latest()
                ->paginate(6); // Ganti get() dengan paginate(), 10 item per halaman
        } else {
            // Lecturer route
            $dosen = auth()->user()->lecturer;

            // Redirect jika data dosen tidak ditemukan
            if (!$dosen) {
                return redirect()->back()
                    ->with('error', 'Data dosen tidak ditemukan. Silakan hubungi admin.');
            }

            $publications = Publication::whereHas('lecturers', function ($query) use ($dosen) {
                $query->where('lecturer_id', $dosen->id);
            })
                ->with('lecturers')
                ->latest()
                ->paginate(6);
        }

        return view(
            request()->routeIs('admin.*') ? 'backend.publikasi.index' : 'dosen.tridharma.publikasi.index',
            compact('publications')
        );
    }

    public function show($id)
    {
        $publikasi = Publication::findOrFail($id);
        return view(request()->routeIs('admin.*') ? 'backend.publikasi.show' : 'dosen.tridharma.publikasi.show', compact('publikasi'));
    }

    public function create()
    {
        // Ubah nama variabel dari $lecturers menjadi $dosen untuk konsistensi
        $dosen = Lecturer::where('id', '!=', auth()->user()->lecturer->id)
            ->get();

        return view('dosen.tridharma.publikasi.create', compact('dosen'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'media' => 'required',
            'media_name' => 'required',
            'issue' => 'required',
            'year' => 'required',
            'page' => 'required',
            'citation' => 'nullable',
            'abstract' => 'required',
            'lecturer_id' => 'nullable|array', // Penulis tambahan bersifat opsional
        ]);

        try {
            // Buat publikasi baru
            $publication = Publication::create([
                'title' => $validatedData['title'],
                'media' => $validatedData['media'],
                'media_name' => $validatedData['media_name'],
                'issue' => $validatedData['issue'],
                'year' => $validatedData['year'],
                'page' => $validatedData['page'],
                'citation' => $validatedData['citation'],
                'abstract' => $validatedData['abstract'],
            ]);

            // Attach dosen yang sedang login
            $publication->lecturers()->attach(auth()->user()->lecturer->id);

            // Attach dosen tambahan jika ada
            if (!empty($validatedData['lecturer_id'])) {
                $publication->lecturers()->attach($validatedData['lecturer_id']);
            }

            if (auth()->user()->hasRole('admin')) {
                return redirect()->to(route('admin.publication.index'))
                    ->with('success', 'Publikasi berhasil ditambahkan');
            }

            return redirect()->to(route('lecturer.publication.index'))
                ->with('success', 'Publikasi berhasil ditambahkan');

        } catch (\Exception $e) {
            \Log::error('Error in store method: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan publikasi. ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $publikasi = Publication::with('lecturers')->findOrFail($id);
        $lecturers = Lecturer::all();

        // Check access for lecturer role
        if (!request()->routeIs('admin.*')) {
            $dosen = auth()->user()->lecturer;

            // Redirect jika data dosen tidak ditemukan
            if (!$dosen) {
                return redirect()->route('lecturer.publication.index')
                    ->with('error', 'Data dosen tidak ditemukan. Silakan hubungi admin.');
            }

            // Check if lecturer is author of the publication
            if (!$publikasi->lecturers->contains('id', $dosen->id)) {
                return redirect()->route('lecturer.publication.index')
                    ->with('error', 'Anda tidak memiliki akses untuk mengedit publikasi ini.');
            }
        }

        return view(
            request()->routeIs('admin.*') ? 'backend.publikasi.edit' : 'dosen.tridharma.publikasi.edit',
            compact('publikasi', 'lecturers')
        );
    }

    public function update(Request $request, $id)
    {
        $publikasi = Publication::with('lecturers')->findOrFail($id);

        // Check access for lecturer role
        if (!request()->routeIs('admin.*')) {
            $dosen = auth()->user()->lecturer;

            // Redirect jika data dosen tidak ditemukan
            if (!$dosen) {
                return redirect()->route('lecturer.research.index')
                    ->with('error', 'Data dosen tidak ditemukan. Silakan hubungi admin.');
            }

            // Check if lecturer is author of the publication
            if (!$publikasi->lecturers->contains('id', $dosen->id)) {
                return redirect()->route('lecturer.research.index')
                    ->with('error', 'Anda tidak memiliki akses untuk mengedit publikasi ini.');
            }
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'media' => 'required|string',
            'media_name' => 'nullable|string',
            'issue' => 'required|string|max:155',
            'year' => 'required',
            'page' => 'nullable|string',
            'citation' => 'nullable|string|max:10',
            'lecturer_id' => 'required|array|min:1',
            'lecturer_id.*' => 'exists:lecturers,id',
        ]);

        // Update publication
        $publikasi->update([
            'title' => $request->title,
            'media' => $request->media,
            'media_name' => $request->media_name,
            'issue' => $request->issue,
            'year' => $request->year,
            'page' => $request->page,
            'citation' => $request->citation,
        ]);

        // Update lecturers
        $publikasi->lecturers()->sync($request->lecturer_id);

        $route = request()->routeIs('admin.*') ? 'admin.publication.index' : 'lecturer.publication.index';
        return redirect()->route($route)->with('success', 'Publikasi berhasil diperbarui');
    }

    public function destroy($id)
    {
        $publikasi = Publication::with('lecturers')->findOrFail($id);

        // Check access for lecturer role
        if (!request()->routeIs('admin.*')) {
            $dosen = auth()->user()->lecturer;

            // Redirect jika data dosen tidak ditemukan
            if (!$dosen) {
                return redirect()->route('lecturer.research.index')
                    ->with('error', 'Data dosen tidak ditemukan. Silakan hubungi admin.');
            }

            // Check if lecturer is author of the publication
            if (!$publikasi->lecturers->contains('id', $dosen->id)) {
                return redirect()->route('lecturer.research.index')
                    ->with('error', 'Anda tidak memiliki akses untuk menghapus publikasi ini.');
            }
        }

        $publikasi->lecturers()->detach(); // Remove all lecturer associations
        $publikasi->delete();

        $route = request()->routeIs('admin.*') ? 'admin.publication.index' : 'lecturer.publication.index';
        return redirect()->route($route)->with('success', 'Publikasi berhasil dihapus');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        $file = $request->file('file');
        Excel::import(new LecturerPublicationImport, $file);

        unlink($file->getRealPath());

        return redirect()->back()->with('success', 'Data Publikasi Dosen berhasil diimport!');
    }

    public function importPkm(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        $file = $request->file('file');
        Excel::import(new LecturerPkmImport, $file);

        unlink($file->getRealPath());

        return redirect()->back()->with('success', 'Data Pengabdian Dosen berhasil diimport!');
    }

    public function importPenunjang(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        $file = $request->file('file');
        Excel::import(new LecturerPenunjangImport, $file);

        unlink($file->getRealPath());

        return redirect()->back()->with('success', 'Data Penunjang Dosen berhasil diimport!');
    }

}