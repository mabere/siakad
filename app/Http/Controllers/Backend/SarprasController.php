<?php

namespace App\Http\Controllers\Backend;

use App\Models\Sarpras;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SarprasController extends Controller
{
    public function index()
    {
        $sarpras = Sarpras::all();
        return view('backend.fasilitas.sarpras', compact('sarpras'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'fungsi' => 'required|string|max:255',
            'kondisi' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        Sarpras::create($validated);

        return redirect()->route('admin.sarpras.index')
            ->with('success', 'Data sarana prasarana berhasil ditambahkan');
    }

    public function update(Request $request, Sarpras $sarpra)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'fungsi' => 'required|string|max:255',
            'kondisi' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $sarpra->update($validated);

        return redirect()->route('admin.sarpras.index')
            ->with('success', 'Data sarana prasarana berhasil diperbarui');
    }

    public function destroy(Sarpras $sarpra)
    {
        $sarpra->delete();
        return redirect()->route('admin.sarpras.index')
            ->with('success', 'Data sarana prasarana berhasil dihapus');
    }

}