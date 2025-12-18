<?php

namespace App\Http\Controllers\Lecturer;

use DateTime;
use App\Models\Course;
use BaconQrCode\Writer;
use App\Models\Lecturer;
use App\Models\Schedule;
use App\Models\StudyPlan;
use App\Helpers\KrsHelper;
use App\Models\Attendance;
use App\Models\MkduCourse;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AttendanceDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

class PresensiKuliahController extends Controller
{
    private $dosen;
    private $currentAcademicYear;

    public function __construct(Request $request)
    {
        $this->dosen = $request->hasSession() ? $request->session()->get('dosen') : null;
        $this->currentAcademicYear = AcademicYear::where('status', 1)->first();
    }

    public function schedules(Request $request)
    {
        $lecturer = auth()->user()->lecturer;
        if (!$lecturer) {
            abort(403, 'Data dosen tidak ditemukan.');
        }
        $ta = getCurrentAcademicYear();
        if (!$ta) {
            abort(404, 'Tahun Akademik saat ini tidak ditemukan.');
        }
        $data = Schedule::whereHas('lecturersInSchedule', function ($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id);
        })->where('academic_year_id', $ta->id)
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
            ])
            ->get();
        return view('dosen.presensi.jadwal', compact('data'));
    }

    public function index()
    {
        $lecturer = auth()->user()->lecturer;
        if (!$lecturer) {
            abort(403, 'Data dosen tidak ditemukan.');
        }

        $cacheKey = 'schedules_lecturer_' . $lecturer->id . '_' . $this->currentAcademicYear->id;
        $data = Cache::remember($cacheKey, 60 * 60, function () use ($lecturer) {
            return $this->getSchedulesForLecturer($lecturer);
        });

        return view('dosen.presensi.index', compact('data'));
    }

    public function show(string $id)
    {
        $lecturer = auth()->user()->lecturer;
        $ta = getCurrentAcademicYear();
        if (!$lecturer) {
            abort(403, 'Data dosen tidak ditemukan.');
        }
        if (!$ta) {
            abort(404, 'Tahun Akademik saat ini tidak ditemukan.');
        }
        $jadwal = Schedule::where('id', $id)
            ->whereHas('lecturersInSchedule', function ($query) use ($lecturer) {
                $query->where('lecturer_id', $lecturer->id);
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
                'lecturersInSchedule',
                'attendances.attendanceDetails',
                'attendances.student',
            ])->firstOrFail();
        $approvedStudentIds = KrsHelper::getApprovedStudentIds($id, $this->currentAcademicYear->id);
        $filteredAttendances = $jadwal->attendances
            ->filter(fn($attendance) => in_array($attendance->student_id, $approvedStudentIds))
            ->sortBy(fn($attendance) => $attendance->student->nim ?? '')
            ->values();
        return view('dosen.presensi.show', [
            'items' => $filteredAttendances,
            'jadwal' => $jadwal,
            'id' => $id,
            'ta' => $this->currentAcademicYear,
            'dosen' => $lecturer,
        ]);
    }

    public function edit(string $id)
    {
        $lecturer = auth()->user()->lecturer;
        if (!$lecturer) {

            abort(403, 'Data dosen tidak ditemukan.');
        }
        $jadwal = Schedule::with([
            'schedulable',
            'schedulable' => function ($morphTo) {
                $morphTo->morphWith([
                    Course::class => ['department'],
                    MkduCourse::class => [],
                ]);
            },
            'kelas',
            'room',
            'lecturersInSchedule',
            'attendances' => function ($query) {
                $query->with('attendanceDetails');
            }
        ])
            ->where('id', $id)
            ->whereHas('lecturersInSchedule', function ($query) use ($lecturer) {
                $query->where('lecturer_id', $lecturer->id);
            })->firstOrFail();
        $currentLecturer = $jadwal->lecturersInSchedule->where('id', $lecturer->id)->first();
        if (!$currentLecturer) {
            return redirect()->route('lecturer.attendance.index')
                ->withErrors('Anda bukan dosen pengampu mata kuliah ini.');
        }
        [$startMeeting, $endMeeting] = $this->getMeetingRange($currentLecturer);
        $currentMeeting = $this->findCurrentMeeting($jadwal->attendances, $startMeeting, $endMeeting);
        return view('dosen.presensi.edit', compact('jadwal', 'currentMeeting', 'startMeeting', 'endMeeting'));
    }

    public function showPresensiForm(string $id, int $pertemuan)
    {
        try {
            $lecturer = auth()->user()->lecturer;
            if (!$lecturer) {
                return redirect()->route('lecturer.attendance.index')->with('error', 'Data dosen tidak ditemukan.');
            }
            $ta = getCurrentAcademicYear();
            $jadwal = Schedule::with([
                'schedulable',
                'schedulable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Course::class => ['department'],
                        MkduCourse::class => [],
                    ]);
                },
                'kelas',
                'room',
                'lecturersInSchedule'
            ])
                ->where('id', $id)
                ->whereHas('lecturersInSchedule', function ($query) use ($lecturer) {
                    $query->where('lecturer_id', $lecturer->id);
                })->firstOrFail();
            $currentLecturerSchedulePivot = $jadwal->lecturersInSchedule->where('id', $lecturer->id)->first()->pivot ?? null;
            if (!$currentLecturerSchedulePivot) {
                return redirect()->route('lecturer.attendance.show', $id)
                    ->with('error', 'Anda tidak terdaftar sebagai pengajar di kelas ini.');
            }
            $startPertemuan = $currentLecturerSchedulePivot->start_pertemuan ?? 1;
            $endPertemuan = $currentLecturerSchedulePivot->end_pertemuan ?? 16;
            if ($pertemuan < $startPertemuan || $pertemuan > $endPertemuan) {
                return redirect()->route('lecturer.attendance.show', $id)
                    ->with('error', "Anda hanya dapat mengisi presensi untuk pertemuan {$startPertemuan} sampai {$endPertemuan}.");
            }
            $approvedStudentIds = KrsHelper::getApprovedStudentIds($id, $ta->id);
            $studyPlans = StudyPlan::where('schedule_id', $id)
                ->where('academic_year_id', $ta->id)
                ->where('status', 'approved')
                ->whereIn('student_id', $approvedStudentIds)
                ->with('student')
                ->get();
            $existingAttendanceDetails = AttendanceDetail::whereIn('attendance_id', function ($query) use ($id, $ta) {
                $query->select('id')
                    ->from('attendances')
                    ->where('schedule_id', $id)
                    ->where('academic_year_id', $ta->id);
            })
                ->where('meeting_number', $pertemuan)
                ->get()
                ->keyBy(function ($item) {
                    return $item->attendance->student_id;
                });
            return view('dosen.presensi.input-presensi', compact(
                'jadwal',
                'pertemuan',
                'studyPlans',
                'existingAttendanceDetails'
            ));
        } catch (\Exception $e) {
            Log::error('Error in showPresensiForm: ' . $e->getMessage(), [
                'exception' => $e,
                'lecturer_id' => $lecturer->id ?? null,
                'schedule_id' => $id,
                'pertemuan' => $pertemuan
            ]);
            return redirect()->route('lecturer.attendance.show', $id)
                ->with('error', 'Terjadi kesalahan saat memuat form presensi: ' . $e->getMessage());
        }
    }

    public function storePresensi(Request $request, string $id, int $pertemuan)
    {
        try {
            DB::beginTransaction();
            $lecturer = auth()->user()->lecturer;
            if (!$lecturer) {
                DB::rollBack();
                return redirect()->route('lecturer.attendance.index')->with('error', 'Data dosen tidak ditemukan.');
            }
            $ta = getCurrentAcademicYear();
            $request->validate([
                'attendance' => 'required|array',
                'attendance.*' => 'required|in:Hadir,Izin,Sakit,Tanpa Keterangan',
            ]);
            $jadwal = Schedule::with('lecturersInSchedule')
                ->where('id', $id)
                ->whereHas('lecturersInSchedule', function ($query) use ($lecturer) {
                    $query->where('lecturer_id', $lecturer->id);
                })->firstOrFail();
            $currentLecturerSchedulePivot = $jadwal->lecturersInSchedule->where('id', $lecturer->id)->first()->pivot ?? null;
            if (!$currentLecturerSchedulePivot) {
                DB::rollBack();
                return redirect()->route('lecturer.attendance.show', $id)
                    ->with('error', 'Anda tidak terdaftar sebagai pengajar di kelas ini.');
            }
            $startPertemuan = $currentLecturerSchedulePivot->start_pertemuan ?? 1;
            $endPertemuan = $currentLecturerSchedulePivot->end_pertemuan ?? 16;
            if ($pertemuan < $startPertemuan || $pertemuan > $endPertemuan) {
                DB::rollBack();
                return redirect()->route('lecturer.attendance.show', $id)
                    ->with('error', "Anda hanya dapat mengisi presensi untuk pertemuan {$startPertemuan} sampai {$endPertemuan}.");
            }
            $validStudyPlans = StudyPlan::where('schedule_id', $id)
                ->where('academic_year_id', $ta->id)
                ->where('status', 'approved')
                ->get();
            foreach ($validStudyPlans as $studyPlan) {
                if (isset($request->attendance[$studyPlan->student_id])) {
                    $attendance = Attendance::firstOrCreate(
                        [
                            'schedule_id' => $id,
                            'student_id' => $studyPlan->student_id,
                            'academic_year_id' => $ta->id,
                            'study_plan_id' => $studyPlan->id
                        ],
                        [

                        ]
                    );
                    AttendanceDetail::updateOrCreate(
                        ['attendance_id' => $attendance->id, 'meeting_number' => $pertemuan],
                        ['status' => $request->attendance[$studyPlan->student_id]]
                    );
                }
            }
            DB::commit();
            return redirect()->route('lecturer.attendance.show', $id)
                ->with('success', 'Presensi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in storePresensi: ' . $e->getMessage(), [
                'exception' => $e,
                'lecturer_id' => $lecturer->id ?? null,
                'schedule_id' => $id,
                'pertemuan' => $pertemuan,
                'request_data' => $request->all()
            ]);
            return redirect()->route('lecturer.attendance.show', $id)
                ->with('error', 'Terjadi kesalahan saat menyimpan presensi: ' . $e->getMessage());
        }
    }

    // public function print($id)
    // {
    //     try {
    //         $ta = getCurrentAcademicYear();
    //         $jadwal = Schedule::with([
    //             'course.department.faculty',
    //             'kelas',
    //             'room',
    //             'lecturersInSchedule',
    //             'attendances' => function ($query) {
    //                 $query->with('attendanceDetails'); // Eager load attendanceDetails
    //             }
    //         ])->find($id);

    //         if (!$jadwal) {
    //             return redirect()->route('lecturer.attendance.index')->with('error', 'Jadwal dengan ID ' . $id . ' tidak ditemukan.');
    //         }

    //         // Urutkan attendances berdasarkan nim student
    //         $jadwal->attendances = $jadwal->attendances->sortBy(function ($attendance) {
    //             return $attendance->student->nim ?? '';
    //         })->values();

    //         $items = $jadwal->attendances;

    //         // Periksa apakah items kosong
    //         if ($items->isEmpty()) {
    //             return redirect()->route('lecturer.attendance.index')->with('warning', 'Tidak ada data presensi untuk jadwal ini.');
    //         }

    //         // Ambil tanggal sekarang
    //         $date = new DateTime('now');
    //         $dateNow = $date->format('d-F-Y');
    //         $tgl = preg_replace("/-/", " ", $dateNow);

    //         // Siapkan data signatures
    //         $signatures = [
    //             [
    //                 'jabatan' => 'Dosen Pengampu 1',
    //                 'nama' => $jadwal->lecturersInSchedule[0]->nama_dosen ?? '',
    //                 'nip' => $jadwal->lecturersInSchedule[0]->nidn ?? ''
    //             ],
    //             [
    //                 'jabatan' => 'Dosen Pengampu 2',
    //                 'nama' => $jadwal->lecturersInSchedule[1]->nama_dosen ?? '',
    //                 'nip' => $jadwal->lecturersInSchedule[1]->nidn ?? ''
    //             ],
    //             [
    //                 'jabatan' => 'Ketua Program Studi',
    //                 'nama' => $jadwal->course->department->kaprodi ?? '',
    //                 'nip' => $jadwal->course->department->nip ?? ''
    //             ]
    //         ];

    //         // Persentase kehadiran dihitung dari attendanceDetails
    //         foreach ($items as $item) {
    //             $totalPresent = $item->attendanceDetails->where('status', 'Hadir')->count();
    //             $item->persentase = number_format(($totalPresent / 16) * 100, 2);
    //         }

    //         // Render ke PDF menggunakan facade
    //         $pdf = \PDF::loadView('dosen.presensi.cetak', [
    //             'items' => $items,
    //             'jadwal' => $jadwal,
    //             'ta' => $ta,
    //             'tgl' => $tgl,
    //             'signatures' => $signatures,
    //         ])->setPaper('a4', request()->input('orientation', 'landscape'));

    //         return $pdf->stream('Daftar_Hadir_Perkuliahan_' . $id . '.pdf');

    //     } catch (\Exception $e) {
    //         Log::error('Error in print: ' . $e->getMessage());
    //         return redirect()->route('lecturer.attendance.index')->with('error', 'Terjadi kesalahan saat mencetak: ' . $e->getMessage());
    //     }
    // }


    public function print(string $id)
    {
        try {
            $ta = getCurrentAcademicYear();
            $jadwal = Schedule::with([
                'schedulable',
                'schedulable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Course::class => ['department.faculty'],
                        MkduCourse::class => [],
                    ]);
                },
                'kelas',
                'room',
                'lecturersInSchedule',
                'attendances.student',
                'attendances.attendanceDetails'
            ])->find($id);

            if (!$jadwal) {
                return redirect()->route('lecturer.attendance.index')->with('error', 'Jadwal dengan ID ' . $id . ' tidak ditemukan.');
            }
            $jadwal->attendances = $jadwal->attendances->sortBy(function ($attendance) {
                return $attendance->student->nim ?? '';
            })->values();
            $items = $jadwal->attendances;
            if ($items->isEmpty()) {
                return redirect()->route('lecturer.attendance.index')->with('warning', 'Tidak ada data presensi untuk jadwal ini.');
            }
            $date = new DateTime('now');
            $dateNow = $date->format('d-F-Y');
            $tgl = preg_replace("/-/", " ", $dateNow);
            $signatures = [];
            foreach ($jadwal->lecturersInSchedule as $index => $lecturer) {
                $signatures[] = [
                    'jabatan' => 'Dosen Pengampu ' . ($index + 1),
                    'nama' => $lecturer->nama_dosen ?? '',
                    'nip' => $lecturer->nidn ?? ''
                ];
            }
            if ($jadwal->schedulable_type === Course::class) {
                $kaprodi = $jadwal->course->department->kaprodi ?? null;
                $kaprodiNip = $jadwal->course->department->nip ?? null;
                $signatures[] = [
                    'jabatan' => 'Ketua Program Studi',
                    'nama' => $kaprodi,
                    'nip' => $kaprodiNip
                ];
            } elseif ($jadwal->schedulable_type === MkduCourse::class) {
                $signatures[] = [
                    'jabatan' => 'Ketua Pusat MKDU',
                    'nama' => 'Nama Pejabat MKDU',
                    'nip' => 'NIP Pejabat MKDU'
                ];
            }
            foreach ($items as $item) {
                $totalPresent = $item->attendanceDetails->where('status', 'Hadir')->count();
                $item->persentase = (16 > 0) ? number_format(($totalPresent / 16) * 100, 2) : 0;
            }
            $pdf = PDF::loadView('dosen.presensi.cetak', [
                'items' => $items,
                'jadwal' => $jadwal,
                'ta' => $ta,
                'tgl' => $tgl,
                'signatures' => $signatures,
            ])->setPaper('a4', request()->input('orientation', 'landscape'));
            return $pdf->stream('Daftar_Hadir_Perkuliahan_' . $id . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error in print: ' . $e->getMessage(), [
                'exception' => $e,
                'schedule_id' => $id
            ]);
            return redirect()->route('lecturer.attendance.index')->with('error', 'Terjadi kesalahan saat mencetak: ' . $e->getMessage());
        }
    }
    private function getScheduleByIdForLecturer($id)
    {
        return Schedule::where('id', $id)
            ->whereHas('lecturersInSchedule', function ($query) {
                $query->where('lecturer_id', $this->dosen->id);
            })
            ->with(['course', 'kelas', 'room', 'lecturer'])
            ->first();
    }

    private function getCurrentLecturerFromSchedule($jadwal)
    {
        $lecturer = auth()->user()->lecturer;

        // Cek di tabel pivot lecturer_schedule
        return $jadwal->lecturersInSchedule()
            ->where('lecturer_id', $lecturer->id)
            ->first();
    }

    private function canEditMeeting(Schedule $jadwal, $pertemuan)
    {
        $lecturer = auth()->user()->lecturer;

        // Cek keberadaan dosen di jadwal menggunakan query builder
        $lecturerSchedule = DB::table('lecturer_schedule')
            ->where('schedule_id', $jadwal->id)
            ->where('lecturer_id', $lecturer->id)
            ->first();

        if (!$lecturerSchedule) {
            return false;
        }

        // Cek urutan dosen
        $isFirstLecturer = DB::table('lecturer_schedule')
            ->where('schedule_id', $jadwal->id)
            ->orderBy('id', 'asc')
            ->first()
            ->lecturer_id == $lecturer->id;

        $startPertemuan = $isFirstLecturer ? 1 : 9;
        $endPertemuan = $isFirstLecturer ? 8 : 16;

        return $pertemuan >= $startPertemuan && $pertemuan <= $endPertemuan;
    }

    public function generateAttendanceQR($scheduleId, $meeting)
    {
        try {
            $lecturer = auth()->user()->lecturer;
            $jadwal = Schedule::where('id', $scheduleId)
                ->whereHas('lecturersInSchedule', function ($query) use ($lecturer) {
                    $query->where('lecturer_id', $lecturer->id);
                })
                ->firstOrFail();

            $isFirstLecturer = $jadwal->lecturersInSchedule->first()->id === $lecturer->id;
            $startPertemuan = $isFirstLecturer ? 1 : 9;
            $endPertemuan = $isFirstLecturer ? 8 : 16;

            if ($meeting < $startPertemuan || $meeting > $endPertemuan) {
                return redirect()->route('lecturer.attendance.edit', $scheduleId)
                    ->with('error', "Anda hanya dapat menghasilkan QR untuk pertemuan {$startPertemuan} sampai {$endPertemuan}");
            }

            $qrData = json_encode([
                'schedule_id' => $scheduleId,
                'meeting' => $meeting,
                'timestamp' => now()->toIso8601String(),
                'lecturer_id' => $lecturer->id
            ]);

            // Gunakan BaconQrCode dengan SVG backend
            $renderer = new ImageRenderer(
                new RendererStyle(300), // Ukuran 300px
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrCodeSvg = $writer->writeString($qrData);

            // Konversi SVG ke base64 untuk ditampilkan
            $qrCodeBase64 = base64_encode($qrCodeSvg);

            // Kembalikan sebagai image inline
            return response("<img src='data:image/svg+xml;base64,{$qrCodeBase64}' alt='QR Code' style='max-width: 200px;'>")
                ->header('Content-type', 'text/html');
        } catch (\Exception $e) {
            Log::error('Error generating QR code: ' . $e->getMessage());
            return redirect()->route('lecturer.attendance.edit', $scheduleId)
                ->with('error', 'Terjadi kesalahan saat menghasilkan QR code: ' . $e->getMessage());
        }
    }

    public function verifyAttendanceQR(Request $request)
    {
        try {
            Log::info('Received QR Data:', ['qr_data' => $request->input('qr_data')]);
            $data = json_decode($request->input('qr_data'), true);

            if (!$data || !isset($data['schedule_id'], $data['meeting'], $data['timestamp'], $data['lecturer_id'])) {
                Log::warning('Invalid QR Data:', ['data' => $data]);
                return response()->json(['error' => 'Data QR tidak valid'], 400);
            }

            $scheduleId = $data['schedule_id'];
            $meeting = $data['meeting'];
            $lecturerId = $data['lecturer_id'];
            $timestamp = $data['timestamp'];

            Log::info('Parsed QR Data:', ['schedule_id' => $scheduleId, 'meeting' => $meeting, 'lecturer_id' => $lecturerId, 'timestamp' => $timestamp]);

            // Validasi waktu (15 menit)
            $scanTime = now();
            $qrTime = new DateTime($timestamp);
            if ($scanTime->diff($qrTime)->i > 15) {
                Log::warning('QR Code Expired:', ['diff' => $scanTime->diff($qrTime)->i]);
                return response()->json(['error' => 'QR code telah kedaluwarsa'], 400);
            }

            $jadwal = Schedule::where('id', $scheduleId)
                ->whereHas('lecturersInSchedule', function ($query) use ($lecturerId) {
                    $query->where('lecturer_id', $lecturerId);
                })
                ->firstOrFail();

            $student = auth()->user()->student;
            if (!$student) {
                Log::warning('Student Authentication Failed');
                return response()->json(['error' => 'Autentikasi mahasiswa gagal'], 401);
            }

            $attendance = Attendance::firstOrCreate([
                'schedule_id' => $scheduleId,
                'student_id' => $student->id,
                'academic_year_id' => $this->currentAcademicYear->id,
                'study_plan_id' => StudyPlan::where('schedule_id', $scheduleId)
                    ->where('student_id', $student->id)
                    ->where('status', 'approved')
                    ->first()->id ?? null
            ]);

            AttendanceDetail::updateOrCreate(
                ['attendance_id' => $attendance->id, 'meeting_number' => $meeting],
                ['status' => 'Hadir', 'updated_at' => now()]
            );

            Log::info('Attendance Recorded:', ['attendance_id' => $attendance->id, 'meeting' => $meeting]);
            return response()->json(['success' => 'Kehadiran berhasil dicatat'], 200);

        } catch (\Exception $e) {
            Log::error('Error verifying QR attendance: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // Private OK
    protected function getSchedulesForLecturer(Lecturer $lecturer)
    {
        $lecturer = auth()->user()->lecturer;
        return Schedule::whereHas('lecturersInSchedule', function ($query) use ($lecturer) {
            $query->where('lecturer_id', $lecturer->id);
        })
            ->where('academic_year_id', $this->currentAcademicYear->id)
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
                'attendances'
            ])
            ->get();
    }

    private function getMeetingRange($lecturerInSchedule)
    {
        return [
            $lecturerInSchedule->pivot->start_pertemuan ?? 1,
            $lecturerInSchedule->pivot->end_pertemuan ?? 16
        ];
    }

    private function findCurrentMeeting($attendances, $start, $end)
    {
        foreach (range($start, $end) as $meeting) {
            $hasDetails = $attendances->flatMap->attendanceDetails->where('meeting_number', $meeting)->isNotEmpty();
            if (!$hasDetails) {
                return $meeting;
            }
        }
        return $end;
    }

}
