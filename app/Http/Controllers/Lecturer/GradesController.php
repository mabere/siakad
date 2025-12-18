<?php

namespace App\Http\Controllers\Lecturer;

use DateTime;
use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\Course;
use App\Models\Student;
use App\Models\Schedule;
use App\Helpers\KrsHelper;
use App\Models\MkduCourse;
use App\Models\AttendanceDetail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Auth\Access\AuthorizationException;

class GradesController extends Controller
{
    public function index(Request $request)
    {
        $dosen = auth()->user()->lecturer;
        $ta = getCurrentAcademicYear();
        $data = Schedule::where('academic_year_id', $ta->id)
            ->whereHas('lecturersInSchedule', function ($query) use ($dosen) {
                $query->where('lecturer_id', $dosen->id);
            })
            ->with([
                'schedulable',
                'schedulable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Course::class => ['department'],
                        MkduCourse::class => [],
                    ]);
                },
                'kelas',
                'room',
                'grades'
            ])
            ->get();

        return view('dosen.nilai.index')->with([
            'data' => $data,
        ]);
    }

    public function show(Request $request, $id)
    {
        $dosen = auth()->user()->lecturer;
        $jadwal = Schedule::with([
            'schedulable' => function ($morphTo) {
                $morphTo->morphWith([
                    Course::class => [],
                    MkduCourse::class => [],
                ]);
            },
            'lecturersInSchedule'
        ])->findOrFail($id);
        if (!$jadwal->lecturersInSchedule->contains('id', $dosen->id)) {
            return redirect()->route('lecturer.nilai.index')
                ->with('error', 'Anda bukan Dosen pengampu Mata kuliah ini.');
        }
        $ta = getCurrentAcademicYear();
        $approvedStudentIds = KrsHelper::getApprovedStudentIds($id, $ta->id);
        $items = Grade::where('schedule_id', $id)
            ->whereIn('student_id', $approvedStudentIds)
            ->with(['student'])
            ->orderBy('student_id', 'asc')
            ->get();

        foreach ($items as $item) {
            $item->attendance_total = $item->attendance ?? 0;
        }
        return view('dosen.nilai.show', compact('items', 'jadwal', 'id', 'ta'));
    }

    public function edit(Request $request, $id)
    {
        $dosen = auth()->user()->lecturer;
        $jadwal = Schedule::with([
            'lecturersInSchedule',
            'schedulable' => function ($morphTo) {
                $morphTo->morphWith([
                    Course::class => [],
                    MkduCourse::class => [],
                ]);
            },
        ])
            ->whereHas('lecturersInSchedule', function ($query) use ($dosen) {
                $query->where('lecturer_id', $dosen->id);
            })
            ->findOrFail($id);
        $ta = getCurrentAcademicYear(); // Gunakan helper untuk tahun akademik aktif

        // Ambil student_id yang sudah ambil KRS jadwal ini & disetujui
        $approvedStudentIds = KrsHelper::getApprovedStudentIds($id, $ta->id);

        // Ambil nilai berdasarkan student_id yang disetujui
        $items = Grade::with(['student'])
            ->where('schedule_id', $id)
            ->whereIn('student_id', $approvedStudentIds)
            ->orderBy('student_id', 'asc')
            ->get();

        // Validasi status penguncian atau melewati deadline
        $isLocked = $items->contains('validation_status', 'locked');
        $isPastDeadline = $items->contains(function ($item) {
            return $item->validation_status === 'dosen_validated' &&
                $item->validation_deadline &&
                now()->greaterThan($item->validation_deadline);
        });

        if ($isLocked || $isPastDeadline) {
            return redirect()->route('lecturer.nilai.show', $id)
                ->with('error', 'Nilai untuk mata kuliah ini telah dikunci atau melewati batas waktu edit.');
        }

        // Hitung dan isi otomatis persentase kehadiran
        foreach ($items as $item) {
            $attendanceDetails = $item->getAttendanceDetails();
            $maxMeeting = $attendanceDetails->max('meeting_number') ?: 0;
            $totalMeetings = min(16, max(1, $maxMeeting));
            $attendanceCount = $attendanceDetails->where('status', 'Hadir')->count();
            $attendancePercentage = ($totalMeetings > 0) ? ($attendanceCount / $totalMeetings) * 100 : 0;

            $item->total_attendance = round($attendancePercentage);
        }

        return view('dosen.nilai.edit', compact('items', 'jadwal', 'id', 'isLocked', 'isPastDeadline'));
    }

    public function update(Request $request, $id)
    {
        $dosen = auth()->user()->lecturer;
        $jadwal = Schedule::with('lecturersInSchedule')
            ->whereHas('lecturersInSchedule', function ($query) use ($dosen) {
                $query->where('lecturer_id', $dosen->id);
            })
            ->findOrFail($id);

        $items = Grade::where('schedule_id', $id)->get();
        $isLocked = $items->contains('validation_status', 'locked');
        if ($isLocked) {
            return redirect()->route('lecturer.nilai.show', $id)
                ->with('error', 'Nilai untuk mata kuliah ini telah dikunci dan tidak dapat diubah.');
        }

        $data = $request->all();
        foreach ($data['idMhs'] as $index => $item) {
            $attendancePercentage = $data['attendance'][$index];
            $Na = ($attendancePercentage * 0.1) +
                ($data['participation'][$index] * 0.15) +
                ($data['assignment'][$index] * 0.15) +
                ($data['mid'][$index] * 0.3) +
                ($data['final'][$index] * 0.3);

            $Na = round($Na);
            $huruf = $this->getGradeLetter($Na);

            Grade::where('student_id', $data['idMhs'][$index])
                ->where('schedule_id', $id)
                ->update([
                    'attendance' => $attendancePercentage,
                    'participation' => $data['participation'][$index],
                    'assignment' => $data['assignment'][$index],
                    'mid' => $data['mid'][$index],
                    'final' => $data['final'][$index],
                    'total' => $Na,
                    'nhuruf' => $huruf,
                ]);
            // Ambil data mahasiswa
            $student = Student::find($data['idMhs'][$index]);

            if ($student) {
                // Hitung total SKS yang diperoleh
                $totalSks = Grade::where('student_id', $student->id)
                    ->whereNotNull('nhuruf') // Hanya nilai yang sudah memiliki huruf (lulus)
                    ->with('schedule.course:id,sks')
                    ->get()
                    ->sum(function ($grade) {
                        return $grade->schedule && $grade->schedule->course ? $grade->schedule->course->sks : 0;
                    });

                // Perbarui total SKS di tabel students
                $student->total_sks = $totalSks;
                $student->save();
            }
        }
        return redirect()->route('lecturer.nilai.show', $id)->with('success', 'Nilai berhasil diinput');
    }

    private function getGradeLetter($score)
    {
        if ($score >= 81)
            return "A";
        elseif ($score >= 66 && $score <= 80)
            return "B";
        elseif ($score >= 51 && $score <= 65)
            return "C";
        elseif ($score >= 36 && $score <= 50)
            return "D";
        elseif ($score >= 0 && $score <= 35)
            return "E";
        else
            return 'T/K';
    }

    public function print(Request $request, $id)
    {
        try {
            $dosen = auth()->user()->lecturer;
            $jadwal = Schedule::with([
                'schedulable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Course::class => ['department.faculty'], // Jika Course, muat department dan faculty
                        MkduCourse::class => [],                   // MkduCourse tidak punya department/faculty
                    ]);
                },
                'lecturersInSchedule' => function ($query) {
                    $query->select('lecturers.*', 'lecturer_schedule.start_pertemuan')
                        ->orderBy('lecturer_schedule.start_pertemuan', 'asc');
                }
            ])
                ->whereHas('lecturersInSchedule', function ($query) use ($dosen) {
                    $query->where('lecturer_id', $dosen->id);
                })
                ->findOrFail($id);

            $items = Grade::where('schedule_id', $id)
                ->with(['student'])
                ->orderBy('student_id', 'asc')
                ->get();

            // Sinkronisasi attendance dengan attendance_details
            $ta = getCurrentAcademicYear();
            foreach ($items as $item) {
                $attendanceDetails = AttendanceDetail::join('attendances', 'attendance_details.attendance_id', '=', 'attendances.id')
                    ->where('attendances.student_id', $item->student_id)
                    ->where('attendances.schedule_id', $id)
                    ->where('attendances.academic_year_id', $ta->id)
                    ->get();
                $maxMeeting = $attendanceDetails->max('meeting_number') ?: 0;
                $totalMeetings = min(16, max(1, $maxMeeting));
                $attendanceCount = $attendanceDetails->where('status', 'Hadir')->count();
                $attendancePercentage = ($totalMeetings > 0) ? ($attendanceCount / $totalMeetings) * 100 : 0;
                if ($item->attendance != round($attendancePercentage)) { // Perbarui hanya jika berbeda
                    $item->attendance = round($attendancePercentage);
                    $item->save();
                }
            }

            if ($items->isEmpty()) {
                return back()->with('error', 'Tidak ada data nilai untuk dicetak.');
            }

            $subjectName = $jadwal->course->name;
            Carbon::setLocale('id');
            $dateNow = Carbon::now()->translatedFormat('d F Y');
            $tgl = $dateNow;

            $signatures = [
                [
                    'jabatan' => 'Dosen Pertama',
                    'nama' => $jadwal->lecturersInSchedule[0]->nama_dosen ?? 'Tidak tersedia',
                    'nip' => $jadwal->lecturersInSchedule[0]->nidn ?? ''
                ],
                [
                    'jabatan' => 'Dosen kedua',
                    'nama' => $jadwal->lecturersInSchedule[1]->nama_dosen ?? '',
                    'nip' => $jadwal->lecturersInSchedule[1]->nidn ?? ''
                ],
                [
                    'jabatan' => 'Dekan',
                    'nama' => $jadwal->course->department->faculty->dekan ?? 'Tidak tersedia',
                    'nip' => $jadwal->course->department->faculty->nip ?? ''
                ],
                [
                    'jabatan' => 'Ketua Program Studi',
                    'nama' => $jadwal->course->department->kaprodi ?? 'Tidak tersedia',
                    'nip' => $jadwal->course->department->nip ?? ''
                ]
            ];

            $pdf = PDF::loadView('dosen.nilai.cetak', compact('items', 'jadwal', 'ta', 'tgl', 'subjectName', 'signatures'))
                ->setPaper('a4', 'portrait')
                ->setOptions(['isHtml5ParserEnabled' => true, 'defaultFont' => 'sans-serif']);

            return $pdf->stream('daftar_nilai_' . $subjectName . '.pdf');
        } catch (\Exception $e) {
            Log::error('Print Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mencetak: ' . $e->getMessage());
        }
    }

    public function validatedByDosen(Request $request, $id)
    {
        Log::info('Validate By Dosen Request: ', [
            'method' => $request->method(),
            'url' => $request->url(),
            'route' => $request->route()->getName(),
            'id' => $id,
            'all' => $request->all()
        ]);
        $jadwal = Schedule::with([
            'grades',
            'lecturersInSchedule',
            'schedulable' => function ($morphTo) {
                $morphTo->morphWith([
                    Course::class => [],
                    MkduCourse::class => [],
                ]);
            }
        ])->findOrFail($id);
        $sampleGrade = $jadwal->grades->first();

        if (!$sampleGrade) {
            return back()->with('error', 'Tidak ada nilai yang bisa divalidasi.');
        }
        try {
            $this->authorize('validatedByDosen', $sampleGrade);
            DB::beginTransaction();
            foreach ($jadwal->grades as $grade) {
                $grade->validation_status = 'dosen_validated';
                $grade->validation_deadline = now()->addDays(config('nilai.deadline_dosen_validation', 14));
                $grade->save();
            }
            DB::commit();
            return redirect()->route('lecturer.nilai.show', $id)
                ->with('success', 'Nilai telah divalidasi oleh dosen dan menunggu persetujuan prodi. Batas waktu edit: ' . $jadwal->grades->first()->validation_deadline->format('d M Y H:i'));
        } catch (AuthorizationException $e) {
            Log::error('Authorization Error during Dosen Validation: ' . $e->getMessage());
            return back()->with('error', 'Anda tidak memiliki hak akses atau nilai sudah divalidasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Validate by Dosen Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat validasi oleh dosen: ' . $e->getMessage());
        }
    }


}
