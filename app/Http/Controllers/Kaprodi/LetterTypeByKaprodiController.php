<?php

namespace App\Http\Controllers\Kaprodi;

use App\Models\LetterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LetterTypeByKaprodiController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('dekan')) {
            $facultyId = $user->lecturer->faculty_id;
            $tipeSurats = LetterType::where('level', 'faculty')
                ->whereExists(function ($subQuery) use ($facultyId) {
                    $subQuery->select(DB::raw(1))
                        ->from('letter_type_assignments')
                        ->whereColumn('letter_type_assignments.letter_type_id', 'letter_types.id')
                        ->where('letter_type_assignments.faculty_id', $facultyId);
                })
                ->paginate(10);
        } elseif ($user->hasRole('kaprodi')) {
            $departmentId = $user->lecturer->department_id;
            $tipeSurats = LetterType::where('level', 'department')
                ->whereExists(function ($subQuery) use ($departmentId) {
                    $subQuery->select(DB::raw(1))
                        ->from('letter_type_assignments')
                        ->whereColumn('letter_type_assignments.letter_type_id', 'letter_types.id')
                        ->where('letter_type_assignments.department_id', $departmentId);
                })
                ->paginate(10);
        } elseif ($user->hasRole('admin')) {
            $tipeSurats = LetterType::paginate(10);
        } else {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return view('kaprodi.tipe-surat.index', compact('tipeSurats'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->hasRole('kaprodi') || !$user->lecturer || !$user->lecturer->department_id) {
            abort(403, 'Anda tidak memiliki izin untuk membuat tipe surat.');
        }
        return view('kaprodi.tipe-surat.create');
    }


    public function store(Request $request)
    {
        $user = Auth::user();

        // Validate user permissions
        if (!$user->hasRole('kaprodi') || !$user->lecturer || !$user->lecturer->department_id) {
            abort(403, 'Anda tidak memiliki izin untuk membuat tipe surat.');
        }

        // Validate request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:letter_types,code',
            'description' => 'required|string',
            'required_fields' => 'required|array',
            'needs_approval' => 'boolean',
        ]);

        // Prepare data for letter_types
        $letterTypeData = [
            'name' => $validated['name'],
            'code' => $validated['code'],
            'required_fields' => $validated['required_fields'],
            'needs_approval' => $validated['needs_approval'] ?? false,
            'level' => 'department',
            'for_dosen' => false, // Adjust if form includes this
        ];

        // Prepare data for letter_type_assignments
        $assignmentData = [
            'department_id' => $user->lecturer->department_id,
            'template_name' => $validated['description'],
            'signer_role' => 'Kaprodi',
            'is_active' => 1,
            'approval_flow' => [
                'steps' => [
                    ['step' => 'submit', 'role' => 'student/lecturer', 'action' => 'create'],
                    ['step' => 'review', 'role' => 'kaprodi/staff', 'action' => 'review', 'optional' => true],
                    ['step' => 'approve', 'role' => 'kaprodi', 'action' => 'approve'],
                ],
            ],
        ];

        // Create LetterType and LetterTypeAssignment
        DB::transaction(function () use ($letterTypeData, $assignmentData) {
            $letterType = LetterType::create($letterTypeData);
            $assignmentData['letter_type_id'] = $letterType->id;
            \App\Models\LetterTypeAssignment::create($assignmentData);
        });

        return redirect()->route('kaprodi.letter-types.index')->with('success', 'Tipe surat berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = Auth::user()->load('lecturer.department');

        // Validate user permissions
        if (!$user->hasRole('kaprodi') || !$user->lecturer || !$user->lecturer->department_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit tipe surat ini.');
        }

        // Find letter type with department_id check
        $letterType = LetterType::where('id', $id)
            ->whereExists(function ($subQuery) use ($user) {
                $subQuery->select(DB::raw(1))
                    ->from('letter_type_assignments')
                    ->whereColumn('letter_type_assignments.letter_type_id', 'letter_types.id')
                    ->where('letter_type_assignments.department_id', $user->lecturer->department_id);
            })
            ->firstOrFail();

        return view('kaprodi.tipe-surat.edit', compact('letterType'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user()->load('lecturer.department');

        // Validate user permissions
        if (!$user->hasRole('kaprodi') || !$user->lecturer || !$user->lecturer->department_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengupdate tipe surat ini.');
        }

        // Validate request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:letter_types,code,' . $id,
            'description' => 'required|string',
            'required_fields' => 'required|array',
            'needs_approval' => 'boolean',
        ]);

        // Find letter type with department_id check
        $letterType = LetterType::where('id', $id)
            ->whereExists(function ($subQuery) use ($user) {
                $subQuery->select(DB::raw(1))
                    ->from('letter_type_assignments')
                    ->whereColumn('letter_type_assignments.letter_type_id', 'letter_types.id')
                    ->where('letter_type_assignments.department_id', $user->lecturer->department_id);
            })
            ->firstOrFail();

        // Prepare data for letter_types
        $letterTypeData = [
            'name' => $validated['name'],
            'code' => $validated['code'],
            'required_fields' => $validated['required_fields'],
            'needs_approval' => $validated['needs_approval'] ?? false,
            'level' => 'department',
            'for_dosen' => $letterType->for_dosen, // Preserve existing value
        ];

        // Prepare data for letter_type_assignments
        $assignmentData = [
            'letter_type_id' => $letterType->id,
            'department_id' => $user->lecturer->department_id,
            'template_name' => $validated['description'],
            'signer_role' => 'Kaprodi',
            'is_active' => 1,
            'approval_flow' => [
                'steps' => [
                    ['step' => 'submit', 'role' => 'student/lecturer', 'action' => 'create'],
                    ['step' => 'review', 'role' => 'kaprodi/staff', 'action' => 'review', 'optional' => true],
                    ['step' => 'approve', 'role' => 'kaprodi', 'action' => 'approve'],
                ],
            ],
        ];

        // Update letter_types and letter_type_assignments
        DB::transaction(function () use ($letterType, $letterTypeData, $assignmentData) {
            $letterType->update($letterTypeData);
            \App\Models\LetterTypeAssignment::updateOrCreate(
                ['letter_type_id' => $letterType->id, 'department_id' => $assignmentData['department_id']],
                $assignmentData
            );
        });

        return redirect()->route('kaprodi.letter-types.index')
            ->with('success', 'Tipe surat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = Auth::user()->load('lecturer.department');

        // Validate user permissions
        if (!$user->hasRole('kaprodi') || !$user->lecturer || !$user->lecturer->department_id) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus tipe surat ini.');
        }

        // Find letter type with department_id check
        $letterType = LetterType::where('id', $id)
            ->whereExists(function ($subQuery) use ($user) {
                $subQuery->select(DB::raw(1))
                    ->from('letter_type_assignments')
                    ->whereColumn('letter_type_assignments.letter_type_id', 'letter_types.id')
                    ->where('letter_type_assignments.department_id', $user->lecturer->department_id);
            })
            ->firstOrFail();

        // Check for associated letter requests
        if ($letterType->requests()->exists()) {
            return redirect()->back()->with('error', 'Tipe surat ini tidak dapat dihapus karena sudah digunakan.');
        }

        // Delete letter type and associated assignments
        DB::transaction(function () use ($letterType) {
            \App\Models\LetterTypeAssignment::where('letter_type_id', $letterType->id)->delete();
            $letterType->delete();
        });

        return redirect()->route('kaprodi.letter-types.index')->with('success', 'Tipe surat berhasil dihapus.');
    }

}