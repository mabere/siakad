<?php

namespace App\Http\Controllers\Dekan;

use App\Models\LetterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LetterTypeByDekanController extends Controller
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

        return view('dekan.tipe-surat.index', compact('tipeSurats'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->hasRole('dekan')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat tipe surat.');
        }

        return view('dekan.tipe-surat.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

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
            'level' => null,
            'for_dosen' => false, // Adjust based on form if needed
        ];

        // Prepare data for letter_type_assignments
        $assignmentData = [
            'template_name' => $validated['description'],
            'is_active' => 1, // Default to active
            'approval_flow' => [
                'steps' => [
                    ['step' => 'submit', 'role' => 'student/lecturer', 'action' => 'create'],
                    ['step' => 'review', 'role' => 'kaprodi/staff', 'action' => 'review', 'optional' => true],
                    ['step' => 'approve', 'role' => null, 'action' => 'approve'],
                ],
            ],
        ];

        // Determine role-specific fields
        if ($user->hasRole('dekan')) {
            $letterTypeData['level'] = 'faculty';
            $assignmentData['faculty_id'] = $user->lecturer->faculty_id;
            $assignmentData['signer_role'] = 'Dekan';
            $assignmentData['approval_flow']['steps'][2]['role'] = 'dekan';
        } elseif ($user->hasRole('kaprodi')) {
            $letterTypeData['level'] = 'department';
            $assignmentData['department_id'] = $user->lecturer->department_id;
            $assignmentData['signer_role'] = 'Kaprodi';
            $assignmentData['approval_flow']['steps'][2]['role'] = 'kaprodi';
        } elseif ($user->hasRole('admin')) {
            $letterTypeData['level'] = 'university';
            $assignmentData['signer_role'] = 'Rektor';
            $assignmentData['approval_flow']['steps'][2]['role'] = 'rektor';
        } elseif ($user->hasRole('kepala_unit')) {
            $letterTypeData['level'] = 'unit';
            $assignmentData['unit_id'] = $user->unit->id;
            $assignmentData['signer_role'] = 'Kepala Unit';
            $assignmentData['approval_flow']['steps'][2]['role'] = 'kepala_unit';
        } else {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk membuat tipe surat.');
        }

        // Create LetterType and LetterTypeAssignment
        DB::transaction(function () use ($letterTypeData, $assignmentData) {
            $letterType = LetterType::create($letterTypeData);
            $assignmentData['letter_type_id'] = $letterType->id;
            \App\Models\LetterTypeAssignment::create($assignmentData);
        });

        return redirect()->route('dekan.letter-types.index')->with('success', 'Tipe surat berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = Auth::user()->load('lecturer.department');

        if (!$user->hasRole('dekan') || !$user->lecturer || !$user->lecturer->department || !$user->lecturer->department->faculty_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit tipe surat ini.');
        }

        $letterType = LetterType::where('id', $id)
            ->whereExists(function ($subQuery) use ($user) {
                $subQuery->select(DB::raw(1))
                    ->from('letter_type_assignments')
                    ->whereColumn('letter_type_assignments.letter_type_id', 'letter_types.id')
                    ->where('letter_type_assignments.faculty_id', $user->lecturer->department->faculty_id);
            })
            ->firstOrFail();

        return view('dekan.tipe-surat.edit', compact('letterType'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user()->load('lecturer.department');

        // Validate user permissions
        if (!$user->hasRole('dekan') || !$user->lecturer || !$user->lecturer->department || !$user->lecturer->department->faculty_id) {
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

        // Find letter type with faculty_id check
        $letterType = LetterType::where('id', $id)
            ->whereExists(function ($subQuery) use ($user) {
                $subQuery->select(DB::raw(1))
                    ->from('letter_type_assignments')
                    ->whereColumn('letter_type_assignments.letter_type_id', 'letter_types.id')
                    ->where('letter_type_assignments.faculty_id', $user->lecturer->department->faculty_id);
            })
            ->firstOrFail();

        // Prepare data for letter_types
        $letterTypeData = [
            'name' => $validated['name'],
            'code' => $validated['code'],
            'required_fields' => $validated['required_fields'],
            'needs_approval' => $validated['needs_approval'] ?? false,
            'level' => 'faculty',
            'for_dosen' => $letterType->for_dosen, // Preserve existing value
        ];

        // Prepare data for letter_type_assignments
        $assignmentData = [
            'letter_type_id' => $letterType->id,
            'faculty_id' => $user->lecturer->department->faculty_id,
            'template_name' => $validated['description'],
            'signer_role' => 'Dekan',
            'is_active' => 1,
            'approval_flow' => [
                'steps' => [
                    ['step' => 'submit', 'role' => 'student/lecturer', 'action' => 'create'],
                    ['step' => 'review', 'role' => 'kaprodi/staff', 'action' => 'review', 'optional' => true],
                    ['step' => 'approve', 'role' => 'dekan', 'action' => 'approve'],
                ],
            ],
        ];

        // Update letter_types and letter_type_assignments
        DB::transaction(function () use ($letterType, $letterTypeData, $assignmentData) {
            $letterType->update($letterTypeData);
            \App\Models\LetterTypeAssignment::updateOrCreate(
                ['letter_type_id' => $letterType->id, 'faculty_id' => $assignmentData['faculty_id']],
                $assignmentData
            );
        });

        return redirect()->route('dekan.letter-types.index')
            ->with('success', 'Tipe surat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = Auth::user()->load('lecturer.department');

        // Validate user permissions
        if (!$user->hasRole('dekan') || !$user->lecturer || !$user->lecturer->department || !$user->lecturer->department->faculty_id) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus tipe surat ini.');
        }

        // Find letter type with faculty_id check
        $letterType = LetterType::where('id', $id)
            ->whereExists(function ($subQuery) use ($user) {
                $subQuery->select(DB::raw(1))
                    ->from('letter_type_assignments')
                    ->whereColumn('letter_type_assignments.letter_type_id', 'letter_types.id')
                    ->where('letter_type_assignments.faculty_id', $user->lecturer->department->faculty_id);
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

        return redirect()->route('dekan.letter-types.index')->with('success', 'Tipe surat berhasil dihapus.');
    }


}
