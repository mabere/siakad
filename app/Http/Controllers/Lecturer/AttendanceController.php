<?php

namespace App\Http\Controllers\Lecturer;

use DateTime;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $dosen = $request->session()->get('dosen');
        $ta = getCurrentAcademicYear(); // ambil tahun akademik
        $data = Schedule::where('lecturer_id', $dosen->id)->where('academic_year_id', $ta->id)->with('course')->get();

        return view('dosen.presensi.index')->with([
            'data' => $data,
        ]);
    }

    public function print($id)
    {
        try {
            $ta = getCurrentAcademicYear();
            $jadwal = Schedule::with([
                'course.department.faculty',
                'kelas',
                'room',
                'lecturersInSchedule'
            ])->findOrFail($id);

            $items = Attendance::where('schedule_id', $id)->with('student')->get();

            // ambil tgl sekarang
            $date = new DateTime('now');
            $dateNow = $date->format('d-F-Y');
            $tgl = preg_replace("/-/", " ", $dateNow);

            // Siapkan data signatures
            $signatures = [
                [
                    'jabatan' => 'Dosen Pengampu 1',
                    'nama' => $jadwal->lecturersInSchedule[0]->nama_dosen,
                    'nip' => $jadwal->lecturersInSchedule[0]->nidn
                ],
                [
                    'jabatan' => 'Dosen Pengampu 2',
                    'nama' => $jadwal->lecturersInSchedule[1]->nama_dosen ?? '',
                    'nip' => $jadwal->lecturersInSchedule[1]->nidn ?? ''
                ],
                [
                    'jabatan' => 'Ketua Program Studi',
                    'nama' => $jadwal->course->department->kaprodi,
                    'nip' => $jadwal->course->department->nip
                ]
            ];

            return view('dosen.presensi.cetak', [
                'items' => $items,
                'jadwal' => $jadwal,
                'ta' => $ta,
                'tgl' => $tgl,
                'signatures' => $signatures
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in print: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mencetak: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $items = Attendance::where('schedule_id', $id)->with('student')->get();
        $jadwal = Schedule::findOrFail($id);
        $ta = getCurrentAcademicYear();

        return view('dosen.presensi.shows')->with([
            'items' => $items,
            'jadwal' => $jadwal,
            'id' => $id,
            'ta' => $ta,
        ]);
    }

    public function edit(string $id)
    {
        $items = Attendance::where('lecturer_id', $id)->with('student')->get();
        $jadwal = Schedule::findOrFail($id);

        return view('dosen.presensi.edit')->with([
            'items' => $items,
            'jadwal' => $jadwal,
            'id' => $id,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->all();
        foreach ($data['idMhs'] as $index => $item) {
            Attendance::where('student_id', $data['idMhs'][$index])
                ->where('schedule_id', $id)
                ->update([
                    'p1' => $data['p1'][$index],
                    'p2' => $data['p2'][$index],
                    'p3' => $data['p3'][$index],
                    'p4' => $data['p4'][$index],
                    'p5' => $data['p5'][$index],
                    'p6' => $data['p6'][$index],
                    'p7' => $data['p7'][$index],
                    'p8' => $data['p8'][$index],
                    'p9' => $data['p9'][$index],
                    'p10' => $data['p10'][$index],
                    'p11' => $data['p11'][$index],
                    'p12' => $data['p12'][$index],
                    'p13' => $data['p13'][$index],
                    'p14' => $data['p14'][$index],
                    'p15' => $data['p15'][$index],
                    'p16' => $data['p16'][$index],
                    'topik' => $data['topik'][$index],
                    'keterangan' => $data['keterangan'][$index],
                ]);
        }
        return redirect()->route('lecturer.presensi.show', $id)->with('status', 'Kehadiran mahasiswa berhasil disimpan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
