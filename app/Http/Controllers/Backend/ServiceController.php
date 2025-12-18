<?php

namespace App\Http\Controllers\Backend;

use App\Models\Lecturer;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Psy\Util\Str;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        // $dosen = $request->session()->get('dosen');

        $services = Service::with('lecturers')->get();

        return view('backend.pkm.index', compact('services'));
    }

    public function show($id)
    {
        $pkm = Service::findOrFail($id);
        return view(request()->routeIs('admin.*') ? 'backend.pkm.show' : 'dosen.tridharma.pkm.show', compact('pkm'));
    }

    public function edit($id)
    {
        $pkm = Service::findOrFail($id);
        $lecturers = Lecturer::all();
        return view('backend.pkm.edit', compact('pkm', 'lecturers'));
    }

    public function update(Request $request, $id)
    {
        $pkm = Service::findOrFail($id);
        // Gate::authorize('update', $pkm);

        $validatedData = $request->validate([
            'lecturer_id' => 'required|array|min:1',
            'lecturer_id.*' => 'exists:lecturers,id',
            'title' => 'required|string|max:255',
            'pendanaan' => 'required|string|max:255',
            'year' => 'required',
        ]);

        $pkm->update($validatedData);
        $pkm->lecturers()->sync($validatedData['lecturer_id']);
        return redirect()->route(request()->routeIs('admin.*') ? 'admin.pkm.index' : 'lecturer.pkm.index')->with('success', 'Data Pengabdian pada Masyarakat berhasil diperbarui!');
    }

    public function create()
    {
        $dosen = Lecturer::all();
        return view('dosen.tridharma.pkm.create', compact('dosen'));
    }

    public function store(Request $request)
    {
        // dd($request->all);
        $validatedData = $request->validate([
            'lecturer_id' => 'required|array|min:1',
            'lecturer_id.*' => 'exists:lecturers,id',
            'title' => 'required|string|max:255',
            'pendanaan' => 'required|string|max:255',
            'year' => 'required',
        ]);

        $pkm = new Service;
        $pkm->title = $validatedData['title'];
        $pkm->pendanaan = $validatedData['pendanaan'];
        $pkm->year = $validatedData['year'];
        $pkm->save();
        $pkm->lecturers()->attach($validatedData['lecturer_id']);

        if (Auth::user()->role == 1) {
            return redirect()->to(url('admin/pkm'))->with('success', 'Data Pengabdian pada Masyarakat berhasil ditambahkan!');
        } else {
            return redirect()->to(url('dosen/pkm'))->with('success', 'Data Pengabdian pada Masyarakat berhasil ditambahkan!');
        }
    }

}