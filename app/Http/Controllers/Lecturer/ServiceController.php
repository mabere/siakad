<?php

namespace App\Http\Controllers\Lecturer;

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
        $lecturer = auth()->user()->lecturer;

        $services = Service::whereHas('lecturers', function ($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id);
        })->with('lecturers')->get();

        return view(request()->routeIs('admin.*') ? 'backend.pkm.index' : 'dosen.tridharma.pkm.index', compact('services'));
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
        return view(request()->routeIs('admin.*') ? 'backend.pkm.edit' : 'dosen.tridharma.pkm.edit', compact('pkm', 'lecturers'));
    }

    public function update(Request $request, $id)
    {
        $pkm = Service::findOrFail($id);

        if (!auth()->user()->can('update', $pkm)) {
            abort(403, 'This action is unauthorized.');
        }

        $validatedData = $request->validate([
            'lecturer_id' => 'required|array|min:1',
            'lecturer_id.*' => 'exists:lecturers,id',
            'title' => 'required|string|max:255',
            'pendanaan' => 'required|string|max:255',
            'year' => 'required',
        ]);

        $pkm->update($validatedData);
        $pkm->lecturers()->sync($validatedData['lecturer_id']);

        if (auth()->user()->hasRole('admin')) {
            return redirect()->to(route('admin.pkm.index'))
                ->with('success', 'Data PKM berhasil diperbarui!');
        }

        return redirect()->to(route('lecturer.pkm.index'))
            ->with('success', 'Data PKM berhasil diperbarui!');
    }

    public function create()
    {
        // Get all lecturers except the logged-in user
        $user = Auth::user();
        $dosen = Lecturer::whereNot('id', $user->lecturer->id)->get();
        return view('dosen.tridharma.pkm.create', compact('dosen'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'lecturer_id' => 'nullable|array', // Co-owners are optional
            'lecturer_id.*' => 'exists:lecturers,id',
            'title' => 'required|string|max:255',
            'pendanaan' => 'required|string|max:255',
            'year' => 'required',
        ]);

        $user = Auth::user();
        if (!$user->hasRole('dosen') || !$user->lecturer) {
            return redirect()->back()->with('error', 'Hanya dosen yang dapat membuat PKM.');
        }

        $pkm = new Service;
        $pkm->title = $validatedData['title'];
        $pkm->pendanaan = $validatedData['pendanaan'];
        $pkm->year = $validatedData['year'];
        $pkm->save();

        // Automatically attach logged-in lecturer as owner
        $lecturerIds = [$user->lecturer->id];
        if (!empty($validatedData['lecturer_id'])) {
            $lecturerIds = array_merge($lecturerIds, $validatedData['lecturer_id']);
        }
        $pkm->lecturers()->attach(array_unique($lecturerIds));

        return redirect()->to(url('dosen/pkm'))->with('success', 'Data Pengabdian pada Masyarakat berhasil ditambahkan!');
    }
    public function destroy($id)
    {
        $pkm = Service::findOrFail($id);

        if (!auth()->user()->can('delete', $pkm)) {
            abort(403, 'This action is unauthorized.');
        }

        $pkm->delete();

        if (auth()->user()->hasRole('admin')) {
            return redirect()->to(route('admin.pkm.index'))
                ->with('success', 'Data PKM berhasil dihapus!');
        }

        return redirect()->to(route('lecturer.pkm.index'))
            ->with('success', 'Data PKM berhasil dihapus!');
    }

}