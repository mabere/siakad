<?php

namespace App\Http\Controllers\Backend;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AcademicYearController extends Controller
{
    public function index()
    {
        $data = AcademicYear::all();
        return view('backend.ta.index', compact('data'));
    }

    public function create()
    {
        return view('backend.ta.create');
    }

    public function store(Request $request)
    {
        request()->validate([
            'ta' => 'required',
            'semester' => 'required',
        ]);

        $ta = new AcademicYear;

        $ta->ta = $request->ta;
        $ta->semester = $request->semester;
        $ta->krs_open_date = $request->krs_open_date;
        $ta->krs_close_date = $request->krs_close_date;
        $ta->start_date = $request->start_date;
        $ta->end_date = $request->end_date;
        $ta->status = 0;

        $ta->save();
        return redirect()->route('admin.ta.index')->with('status', 'Data Tahun Akademik berhasil ditambahkan!');
    }

    public function activate(Request $request)
    {
        $item = AcademicYear::where('status', 1)->get();

        if ($item->isNotEmpty()) {
            AcademicYear::where('id', $item[0]->id)->update(['status' => 0]);
        }

        // Validasi input $request->id
        $request->validate([
            'id' => 'required|exists:academic_years,id',
        ]);

        // Update status akademik menjadi 1
        AcademicYear::where('id', $request->id)->update(['status' => 1]);

        return redirect()->route('admin.ta.index')->with('status', 'Tahun Akademik Berhasil Dirubah.');
    }

    public function edit($id)
    {
        $ta = AcademicYear::findOrFail($id);
        return view('backend.ta.edit', compact('ta'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        request()->validate([
            'krs_open_date' => 'required',
            'krs_close_date' => 'required',
        ]);

        $ta = AcademicYear::findOrFail($id);
        $ta->update([
            'krs_open_date' => $request->krs_open_date,
            'krs_close_date' => $request->krs_close_date
        ]);
        return redirect()->route('admin.ta.index')->with('status', 'Data Tahun Akademik berhasil diupdate.');
    }

    public function destroy($id)
    {
        $ta = AcademicYear::findOrFail($id);
        $ta->delete();

        return redirect()->route('admin.ta.index')->with('success', 'Data Tahun Akademik berhasil dihapus.');
    }
}