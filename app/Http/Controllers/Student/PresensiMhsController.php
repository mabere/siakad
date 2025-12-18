<?php

namespace App\Http\Controllers\Student;

use App\Models\AcademicYear;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PresensiMhsController extends Controller
{
    public function index(Request $request)
    {
        $mahasiswa = $request->session()->get('mahasiswa');
        $ta = getCurrentAcademicYear();
        $presensi = Attendance::where('mahasiswa_id', $mahasiswa->id)->where('ta_id', $ta->id)->get();
        return view('mhs.presensi.index')->with([
            'ta' => $ta,
            'presensi' => $presensi,
        ]);
    }
}
