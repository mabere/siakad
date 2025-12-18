<?php

namespace App\Http\Controllers\Staff;

use App\Imports\CourseImportByStaff;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class StaffCourseController extends Controller
{
    public function index()
    {
        $staff = Auth::user()->employee;

        if (!$staff || !$staff->department) {
            return redirect()->route('dashboard')->with('error', 'Data departemen tidak ditemukan.');
        }
        $courses = Course::where('department_id', $staff->department_id)->get();
        return view('staff.mk.index', compact('courses'));
    }

    public function show($id)
    {
        $staff = Auth::user()->employee;

        if (!$staff || !$staff->department_id) {
            return redirect()->route('staff.course.index')->with('error', 'Data departemen tidak ditemukan.');
        }

        $course = Course::where('id', $id)
            ->where('department_id', $staff->department_id)
            ->with('department')
            ->first();

        if (!$course) {
            return redirect()->route('staff.course.index')->with('error', 'Anda tidak memiliki akses ke matakuliah ini.');
        }

        return view('staff.mk.show', compact('course'));
    }

    public function create()
    {
        $staff = Auth::user()->employee;

        if (!$staff || !$staff->department_id) {
            return redirect()->route('staff.mk.index')->with('error', 'Data departemen tidak ditemukan.');
        }

        $prodi = Department::find($staff->department_id); // hanya 1 prodi, jadi tidak perlu get()

        return view('staff.mk.create', compact('prodi'));
    }

    public function store(Request $request)
    {
        $staff = Auth::user()->employee;
        if (!$staff || !$staff->department) {
            return redirect()->route('staff.course.index')->with('error', 'Data departemen tidak ditemukan.');
        }
        $request->validate([
            'name' => 'required|string',
            'code' => 'required|unique:courses,code',
            'sks' => 'required',
            'semester_number' => 'required',
            'kategori' => 'required',
        ]);

        $data = new Course;

        $data->name = $request->name;
        $data->department_id = $staff->department_id;
        $data->code = $request->code;
        $data->sks = $request->sks;
        $data->semester_number = $request->semester_number;
        $data->kategori = $request->kategori;
        $data->save();
        return redirect()->route('staff.course.index')->with('success', 'Data Mata Kuliah berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $staff = Auth::user()->employee;

        $course = Course::where('id', $id)
            ->where('department_id', $staff->department_id)
            ->firstOrFail();

        return view('staff.mk.edit', compact('course'));
    }


    public function update(Request $request, $id)
    {
        $staff = Auth::user()->employee;

        if (!$staff || !$staff->department) {
            return redirect()->route('staff.course.index')->with('error', 'Data departemen tidak ditemukan.');
        }
        $course = Course::where('id', $id)
            ->where('department_id', $staff->department_id)
            ->firstOrFail();

        $request->validate([
            'name' => 'required|string',
            'code' => 'required|unique:courses,code,' . $course->id,
            'sks' => 'required',
            'semester_number' => 'required',
            'kategori' => 'required',
        ]);

        $course->update([
            'name' => $request->name,
            'department_id' => $staff->department_id,
            'code' => $request->code,
            'sks' => $request->sks,
            'semester_number' => $request->semester_number,
            'kategori' => $request->kategori,
        ]);
        return redirect()->route('staff.course.index')->with('success', 'Data Mata Kuliah berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $staff = Auth::user()->employee;
        if (!$staff || !$staff->department) {
            return redirect()->route('staff.course.index')->with('error', 'Data departemen tidak ditemukan.');
        }
        $course = Course::where('id', $id)
            ->where('department_id', $staff->department_id)
            ->firstOrFail();
        $course->delete();
        return redirect()->route('staff.course.index')->with('success', 'Mata Kuliah berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $file = $request->file('file');
        Excel::import(new CourseImportByStaff, $file);
        unlink($file->getRealPath());
        return redirect()->route('staff.course.index')->with('success', 'Data mata kuliah berhasil diimport!');
    }
}