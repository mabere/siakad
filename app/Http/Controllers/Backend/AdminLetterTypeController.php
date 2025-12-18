<?php

namespace App\Http\Controllers\Backend;

use App\Models\Faculty;
use App\Models\Department;
use App\Models\LetterType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class AdminLetterTypeController extends Controller
{
    public function index()
    {
        $types = LetterType::with('faculty')->paginate(10);
        return view('admin.letters.letter-types.index', compact('types'));
    }

    public function create()
    {
        $faculties = Faculty::all(); // Ambil semua fakultas
        $departments = Department::all(); // Ambil semua departemen
        return view('admin.letters.letter-types.create', compact('faculties', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:letter_types,code',
            'template' => 'required|string',
            'level' => 'required|string|in:university,faculty,department',
            'unit_id' => 'nullable|exists:units,id',
            'faculty_id' => 'nullable|exists:faculties,id',
            'department_id' => 'nullable|exists:departments,id',
            'required_fields' => 'required|array',
            'needs_approval' => 'boolean',
        ]);

        $validated['needs_approval'] = $request->has('needs_approval');

        // Tentukan signer_role berdasarkan level
        $validated['signer_role'] = match ($validated['level']) {
            'university' => 'Rektor',
            'faculty' => 'Dekan',
            'department' => 'Kaprodi',
            default => null,
        };

        $validated['level'] = trim($validated['level']); // Pastikan bersih dari spasi tersembunyi
        if ($validated['level'] === 'university') {
            $validated['signer_role'] = 'Rektor';
            $assignmentData['approval_flow']['steps'][2]['role'] = 'rektor';
        } elseif ($validated['level'] === 'faculty') {
            $validated['signer_role'] = 'Dekan';
        } elseif ($validated['level'] === 'department') {
            $validated['signer_role'] = 'Kaprodi';
        } else {
            $validated['signer_role'] = null;
        }

        // Encode required_fields if your DB column is JSON/text
        $validated['required_fields'] = json_encode($validated['required_fields']);

        LetterType::create($validated);

        return redirect()->route('admin.letter-types.index')->with('success', 'Tipe surat berhasil ditambahkan');
    }

    public function edit(LetterType $letterType)
    {
        $letterType->required_fields = is_array($letterType->required_fields)
            ? $letterType->required_fields
            : json_decode($letterType->required_fields, true) ?? [];
        $faculties = Faculty::all(); // Ambil semua fakultas
        $departments = Department::all(); // Ambil semua departemen
        return view('admin.letters.letter-types.edit', compact('letterType', 'faculties', 'departments'));
    }

    public function update(Request $request, LetterType $letterType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', Rule::unique('letter_types', 'code')->ignore($letterType->id)],
            'template' => 'required|string',
            'level' => 'required|string|in:university,faculty,department',
            'faculty_id' => 'nullable|exists:faculties,id',
            'department_id' => 'nullable|exists:departments,id',
            'required_fields' => 'required|array',
            'needs_approval' => 'boolean',
        ]);

        // $validated['needs_approval'] = (bool) $request->has('needs_approval');
        $validated['needs_approval'] = $request->has('needs_approval');

        // Tentukan signer_role berdasarkan level
        $validated['signer_role'] = match ($validated['level']) {
            'university' => 'Rektor',
            'faculty' => 'Dekan',
            'department' => 'Kaprodi',
            default => null,
        };

        // Reset faculty_id dan department_id sesuai level yang dipilih
        if ($validated['level'] === 'faculty') {
            $validated['department_id'] = null; // Reset department_id jika level fakultas
        } elseif ($validated['level'] === 'department') {
            $validated['faculty_id'] = null; // Reset faculty_id jika level prodi
        } elseif ($validated['level'] === 'university') {
            $validated['faculty_id'] = null;
            $validated['department_id'] = null; // Reset keduanya jika level universitas
        }

        $letterType->update($validated);

        return redirect()->route('admin.letter-types.index')->with('success', 'Tipe surat berhasil diperbarui');
    }


    public function destroy(LetterType $letterType)
    {
        // Check if there are any letters using this type
        if ($letterType->requests()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus tipe surat yang sudah digunakan');
        }

        $letterType->delete();

        return redirect()
            ->route('admin.letter-types.index')
            ->with('success', 'Tipe surat berhasil dihapus');
    }
}