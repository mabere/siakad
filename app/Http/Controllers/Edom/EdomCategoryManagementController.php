<?php

namespace App\Http\Controllers\Edom;

use App\Models\Question;
use App\Models\EdomCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EdomCategoryManagementController extends Controller
{
    public function index()
    {
        $categories = EdomCategory::orderBy('value', 'asc')->get();
        return view('admin.edom.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.edom.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:edom_categories,key|max:50',
            'value' => 'required|string|max:255',
        ]);

        EdomCategory::create($validated);

        return redirect()->route('admin.edom.categories.index')
            ->with('success', 'Kategori EDOM berhasil ditambahkan.');
    }

    public function storeCategorys(Request $request)
    {
        try {
            $validated = $request->validate([
                'key' => 'required|string|max:50|unique:edom_categories,key',
                'value' => 'required|string|max:255'
            ]);

            // Simpan ke database atau file konfigurasi
            // Opsi 1: Menggunakan database
            DB::table('edom_categories')->insert([
                'key' => strtoupper($validated['key']),
                'value' => $validated['value'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Opsi 2: Menggunakan file konfigurasi
            // $categories = config('edom.categories', []);
            // $categories[strtoupper($validated['key'])] = $validated['value'];
            // // Update file config

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kategori: ' . $e->getMessage()
            ], 422);
        }
    }

    public function edit(EdomCategory $category)
    {
        return view('admin.edom.categories.edit', compact('category'));
    }

    public function update(Request $request, EdomCategory $category)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:edom_categories,key,' . $category->id . '|max:50',
            'value' => 'required|string|max:255',
        ]);

        $category->update($validated);

        return redirect()->route('admin.edom.categories.index')
            ->with('success', 'Kategori EDOM berhasil diperbarui.');
    }

    public function destroy(EdomCategory $category)
    {
        if (Question::where('category', $category->id)->exists()) {
            return redirect()->back()->with('error', 'Kategori ini tidak dapat dihapus karena masih digunakan di pertanyaan.');
        }

        $category->delete();

        return redirect()->route('admin.edom.categories.index')
            ->with('success', 'Kategori EDOM berhasil dihapus.');
    }
}