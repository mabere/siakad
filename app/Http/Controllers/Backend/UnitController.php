<?php

namespace App\Http\Controllers\Backend;

use App\Models\Unit;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::all();
        return view('backend.unit.index', compact('units'));
    }

    public function create()
    {
        $parentUnits = Unit::all();
        $lecturers = Lecturer::all();
        return view('backend.unit.create', compact('parentUnits', 'lecturers'));
    }

    public function store(Request $request)
    {
            $validated = $request->validate([
                'nama' => 'required|string',
                'level' => 'required|in:Universitas,Fakultas,Program Studi,Lainnya',
                'code' => 'required|unique:units,code',
                'kepala_source' => 'required|in:lecturer,manual',
                'lecturer_id' => 'nullable|exists:lecturers,id|required_if:kepala_source,lecturer',
                'kepala_unit' => 'required_if:kepala_source,manual',
                'nip_kepala' => 'required_if:kepala_source,manual',
                'email' => 'nullable|email',
                'phone' => 'nullable|string',
                'signature_path' => 'nullable|file|mimes:png,jpg,jpeg',
            ]);

        // Proses upload file signature (jika ada)
        if ($request->hasFile('signature_path')) {
            $file = $request->file('signature_path');
            $file->storeAs('public/images/staff', $file->hashName());
            $validated['signature_path'] = $file->hashName();
        }

        $data = $validated;

        if ($data['kepala_source'] === 'lecturer') {
            $lecturer = Lecturer::find($data['lecturer_id']);
            if (!$lecturer) {
                return redirect()->back()->withInput()->with('error', 'Dosen tidak ditemukan.');
            }
            $data['kepala_unit'] = $lecturer->nama_dosen;
            $data['nip_kepala'] = $lecturer->nidn;
        } else {
            // Jika manual, data kepala_unit sudah ada di $validated
            $data['lecturer_id'] = null;
            $data['nip_kepala'] = $request->nip_kepala;
        }

        unset($data['kepala_source']);

        Unit::create($data);

        return redirect()->route('admin.units.index')->with('success', 'Data unit berhasil ditambahkan!');
    }

    public function edit(Unit $unit)
    {
        $lecturers = Lecturer::all();
        return view('backend.unit.edit', compact('unit', 'lecturers'));
    }
    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'nama' => 'required|string',
            'level' => 'required|in:Universitas,Fakultas,Program Studi,Lainnya',
            'code' => 'required',
            'kepala_source' => 'required|in:lecturer,manual',
            'lecturer_id' => 'nullable|exists:lecturers,id|required_if:kepala_source,lecturer',
            'kepala_unit' => 'required_if:kepala_source,manual',
            'nip_kepala' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'signature_path' => 'nullable|file|mimes:png,jpg,jpeg',
        ]);

        if ($request->hasFile('signature_path')) {
            // Hapus file signature lama jika ada
            if ($unit->signature_path) {
                Storage::delete('public/images/staff/' . $unit->signature_path);
            }

            $file = $request->file('signature_path');
            $file->storeAs('public/images/staff', $file->hashName());
            $validated['signature_path'] = $file->hashName();
        }

        $data = $validated;

        if ($data['kepala_source'] === 'lecturer') {
            $lecturer = Lecturer::find($data['lecturer_id']);
            if (!$lecturer) {
                return redirect()->back()->withInput()->with('error', 'Dosen tidak ditemukan.');
            }
            $data['kepala_unit'] = $lecturer->id;
            $data['nip_kepala'] = $lecturer->nidn;
        } else {
            $data['lecturer_id'] = null;
            $data['nip_kepala'] = $request->nip_kepala;
        }

        unset($data['kepala_source']);

        $unit->update($data);

        return redirect()->route('admin.units.index')->with('success', 'Data unit berhasil diperbarui!');
    }


    public function show(Unit $unit)
    {
        return view('backend.unit.show', compact('unit'));
    }

    public function destroy(Unit $unit)
    {
        if ($unit->signature_path) {
            try {
                Storage::delete('public/images/staff/' . $unit->signature_path);
            } catch (\Exception $e) {
                Log::error('Gagal menghapus file signature: ' . $e->getMessage());
            }
        }

        $unit->delete();

        return redirect()->route('admin.units.index')
            ->with('success', 'Data unit berhasil dihapus');
    }

    public function ubahGambar(Request $request, $id)
    {
        // dd($request->all());
        $unit = Unit::findOrFail($id);
        $request->validate([
            'signature_path' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($request->hasFile('signature_path')) {
            if ($unit->signature_path) {
                Storage::disk('public')->delete('images/staff/' . $unit->signature_path);
            }
            $path = $request->file('signature_path')->store('images/staff', 'public');
            $unit->signature_path = basename($path);
        }
        $unit->save();
        return redirect()->back()->with('success', 'Tanda tangan berhasil diperbarui.');
    }

}