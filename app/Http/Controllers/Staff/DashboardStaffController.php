<?php

namespace App\Http\Controllers\Staff;

use App\Models\Department;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\RedirectResponse;

class DashboardStaffController extends Controller
{
    public function department()
    {
        $user = Auth::user();
        if (!$user->employee) {
            return redirect()->back()->with('error', 'Data employee tidak ditemukan.');
        }
        if ($user->employee->level === 'faculty') {
            if (!$user->employee->faculty_id) {
                return redirect()->back()->with('error', 'Data fakultas KTU tidak ditemukan.');
            }
            $departments = Department::where('faculty_id', $user->employee->faculty_id)->get();
            return view('staff.prodi', ['departments' => $departments]);
        } elseif ($user->employee->level === 'department') {
            $department = $user->employee->department;
            if (!$department) {
                return redirect()->back()->with('error', 'Data departemen tidak ditemukan.');
            }
            return view('staff.prodi', ['departments' => collect([$department])]);
        }
        return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    public function show(Department $department)
    {
        $user = Auth::user();

        if (!$user->employee) {
            return redirect()->back()->with('error', 'Data employee tidak ditemukan.');
        }
        if ($user->employee->level === 'department') {
            $staffDepartment = optional($user->employee)->department;
            if (!$staffDepartment || $department->id !== $staffDepartment->id) {
                return redirect()->route('staff.departments.index')
                    ->with('error', 'Anda tidak memiliki akses ke program studi ini.');
            }
        } elseif ($user->employee->level === 'faculty') {
            if (!$user->employee->faculty_id || $department->faculty_id !== $user->employee->faculty_id) {
                return redirect()->route('staff.departments.index')
                    ->with('error', 'Anda tidak memiliki akses ke program studi ini.');
            }
        } else {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
        $department->load('lecturer');
        return view('staff.detail-prodi', compact('department'));
    }

}