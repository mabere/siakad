<?php

namespace App\Http\Controllers;

use App\Models\ThesisExam;
use Illuminate\Http\Request;
use PDF;

class ThesisScheduleController extends Controller
{
    public function index()
    {
        $exams = ThesisExam::with([
            'thesis.student',
            'thesis.supervisions.supervisor.user',
            'examiners.lecturer.user',
        ])->whereNotNull('scheduled_at')
            ->orderBy('scheduled_at')
            ->get();


        return view('backend.skripsi.jadwal', compact('exams'));
    }

    public function cetak(Request $request)
    {
        $date = $request->input('date');
        $examType = $request->input('exam_type');

        $query = ThesisExam::with('thesis.student.department.faculty');

        if ($date) {
            $query->whereDate('scheduled_at', $date);
        }

        if ($examType) {
            $query->where('exam_type', $examType);
        }

        $exams = $query->orderBy('scheduled_at')->get();

        if ($exams->isEmpty()) {
            return "Tidak ada jadwal ujian untuk kriteria tersebut.";
        }

        $pdf = PDF::loadView('backend.skripsi.cetak-jadwal', compact('exams'))->setPaper('a4', 'landscape');
        return $pdf->stream('jadwal-ujian-skripsi.pdf');
    }

    public function cetakSk(Request $request)
    {
        $date = $request->input('date');
        $examType = $request->input('exam_type');

        $query = ThesisExam::with([
            'thesis.student.department.faculty',
            'thesis.supervisions.supervisor',
            'examiners.lecturer'
        ]);

        if ($date) {
            $query->whereDate('scheduled_at', $date);
        }

        if ($examType) {
            $query->where('exam_type', $examType);
        }

        $exams = $query->orderBy('scheduled_at')->get();

        if ($exams->isEmpty()) {
            return "Tidak ada jadwal ujian untuk kriteria tersebut.";
        }
        $firstExam = $exams->first();
        $fakultas = optional($firstExam->thesis->student->department->faculty);

        $dekan = (object) [
            'nama_dosen' => 'Dr. Anas, S.Ag., M.Pd.',
            'nidn' => '0912027502'
        ];

        $fakultasNama = $fakultas->nama ?? 'Fakulas Keguruan dan Ilmu Pendidikan';
        $fakultasWebsite = $fakultas->website ?? 'https://fkip-Unilaki.ac.id/';
        $fakultasEmail = $fakultas->Email ?? 'info@fkip-Unilaki.ac.id';

        $pdf = PDF::loadView('backend.skripsi.cetak-sk', compact('fakultasNama', 'exams', 'dekan', 'fakultasWebsite', 'fakultasEmail'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('sk-ujian-skripsi.pdf');
    }
}