<?php

namespace App\Http\Controllers\Lecturer;

use DateTime;
use App\Models\Bap;
use App\Models\Course;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\MkduCourse;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use BaconQrCode\Renderer\ImageRenderer;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class BapController extends Controller
{
    public function index(Request $request)
    {
        $dosen = auth()->user()->lecturer;
        $ta = getCurrentAcademicYear();
        if (!$ta) {
            return redirect()->back()
                ->with('error', 'Tahun akademik aktif belum diatur');
        }
        try {
            $jadwal = Schedule::where('academic_year_id', $ta->id)
                ->whereHas('lecturersInSchedule', function ($query) use ($dosen) {
                    $query->where('lecturer_id', $dosen->id);
                })
                ->with(['schedulable', 'kelas', 'lecturersInSchedule'])
                ->get();
            return view('dosen.bap.index', compact('jadwal'));
        } catch (\Exception $e) {
            Log::error('Error in BapController@index: ' . $e->getMessage(), [
                'exception' => $e,
                'lecturer_id' => $dosen->id ?? null,
                'academic_year_id' => $ta->id ?? null,
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat daftar mata kuliah: ' . $e->getMessage());
        }
    }

    public function show(Request $request, string $id)
    {
        $dosen = auth()->user()->lecturer;
        try {
            $jadwal = Schedule::whereHas('lecturersInSchedule', function ($query) use ($dosen) {
                $query->where('lecturer_id', $dosen->id);
            })
                ->with([
                    'schedulable',
                    'schedulable' => function ($morphTo) {
                        $morphTo->morphWith([
                            Course::class => ['department'],
                        ]);
                    },
                    'lecturersInSchedule'
                ])->findOrFail($id);
            if (!class_exists(Bap::class)) {
                Log::error('Class Bap not found in show method for schedule_id: ' . $id);
                return redirect()->back()->with('error', 'Kelas Bap tidak ditemukan. Periksa model.');
            }
            $baps = Bap::where('schedule_id', $id)->pluck('pertemuan');
            $lastFilledPertemuan = !empty($baps->toArray()) ? max($baps->toArray()) : 0;
            $allPreviousFilled = [];
            for ($i = 1; $i <= 16; $i++) {
                $tempAllPreviousFilled = true;
                for ($j = 1; $j < $i; $j++) {
                    if (!$baps->contains($j) && ($j <= 8 || ($j > 8 && $dosen->id != $jadwal->lecturersInSchedule[0]->id))) {
                        $tempAllPreviousFilled = false;
                        break;
                    }
                }
                $allPreviousFilled[$i] = $tempAllPreviousFilled;
            }
            $attendances = Attendance::where('schedule_id', $id)->with('attendanceDetails')->get();
            $attendanceStatus = [];
            foreach ($attendances as $attendance) {
                $status = [];
                for ($k = 1; $k <= 16; $k++) {
                    $detail = $attendance->attendanceDetails->where('meeting_number', $k)->first();
                    $status[$k] = $detail ? $detail->status : 'Tanpa Keterangan';
                }
                $attendanceStatus[$attendance->student_id] = $status;
            }
            return view('dosen.bap.show', compact('dosen', 'jadwal', 'baps', 'lastFilledPertemuan', 'attendanceStatus', 'allPreviousFilled'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('lecturer.bap.index')->with('error', 'Jadwal dengan ID ' . $id . ' tidak ditemukan atau Anda tidak memiliki akses.');
        } catch (\Exception $e) {
            Log::error('Error in BapController@show: ' . $e->getMessage(), [
                'exception' => $e,
                'schedule_id' => $id,
                'lecturer_id' => $dosen->id ?? null,
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat BAP: ' . $e->getMessage());
        }
    }

    public function create(string $id, int $pertemuan)
    {
        try {
            $jadwal = Schedule::with([
                'schedulable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Course::class => ['department'],
                        MkduCourse::class => [],
                    ]);
                },
                'lecturersInSchedule'
            ])->findOrFail($id);

            $lecturer = auth()->user()->lecturer;
            $isAllowed = false;

            // Ambil Dosen sesuai pertemuan
            foreach ($jadwal->lecturersInSchedule as $lecturerInSchedule) {
                if ($lecturerInSchedule->id === $lecturer->id) {
                    if ($pertemuan >= $lecturerInSchedule->pivot->start_pertemuan && $pertemuan <= $lecturerInSchedule->pivot->end_pertemuan) {
                        // Logika validasi urutan pengisian BAP
                        if ($pertemuan > 1) {
                            $previousPertemuan = $pertemuan - 1;
                            $bapPrevious = Bap::where('schedule_id', $id)->where('pertemuan', $previousPertemuan)->first();

                            // Aturan: Jika pertemuan 9 dan belum ada BAP pertemuan 8, blokir.
                            if ($pertemuan === 9) {
                                $bapPertemuan8 = Bap::where('schedule_id', $id)->where('pertemuan', 8)->first();
                                if (!$bapPertemuan8) {
                                    return redirect()->back()->with('error', 'Silakan isi BAP pertemuan 8 terlebih dahulu oleh dosen pertama.');
                                }
                            }

                            // Jika pertemuan > 1 (dan bukan pertemuan 9 yang sudah ditangani),
                            // dan BAP pertemuan sebelumnya tidak ada, maka tidak diizinkan.
                            if (!$bapPrevious && $pertemuan !== 9) {
                                return redirect()->back()->with('error', "Silakan isi BAP pertemuan {$previousPertemuan} terlebih dahulu.");
                            }
                        }
                        $isAllowed = true;
                        break; // Dosen sudah ditemukan dan pertemuan dalam rentangnya, keluar dari loop
                    }
                }
            }

            if (!$isAllowed) {
                return redirect()->back()->with('error', 'Anda tidak berhak mengisi BAP untuk pertemuan ini atau prasyarat belum terpenuhi.');
            }

            // Ambil data BAP jika sudah ada (untuk mode edit)
            $bap = Bap::where('schedule_id', $id)->where('pertemuan', $pertemuan)->first();

            return view('dosen.bap.create', compact('jadwal', 'pertemuan', 'bap'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('lecturer.bap.index')->with('error', 'Jadwal dengan ID ' . $id . ' tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Error in BapController@create for schedule_id ' . $id . ' and pertemuan ' . $pertemuan . ': ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat formulir BAP: ' . $e->getMessage());
        }
    }

    public function store(Request $request, string $scheduleId, int $pertemuan)
    {
        $lecturer = auth()->user()->lecturer;
        try {
            $schedule = Schedule::with([
                'lecturersInSchedule' => function ($query) use ($lecturer) {
                    $query->where('lecturer_id', $lecturer->id);
                }
            ])->findOrFail($scheduleId);

            $request->validate([
                'topik' => 'required|string|max:255',
                'keterangan' => 'nullable|string',
            ]);
            $isAllowed = false;
            foreach ($schedule->lecturersInSchedule as $lecturerItem) {
                if (
                    $lecturerItem->id === $lecturer->id &&
                    $pertemuan >= $lecturerItem->pivot->start_pertemuan &&
                    $pertemuan <= $lecturerItem->pivot->end_pertemuan
                ) {
                    if ($pertemuan > 1) {
                        $previousPertemuan = $pertemuan - 1;
                        $bapPrevious = Bap::where('schedule_id', $scheduleId)->where('pertemuan', $previousPertemuan)->first();
                        if ($pertemuan === 9) {
                            $bapPertemuan8 = Bap::where('schedule_id', $scheduleId)->where('pertemuan', 8)->first();
                            if (!$bapPertemuan8) {
                                return redirect()->back()->with('error', 'Silakan isi BAP pertemuan 8 terlebih dahulu oleh dosen pertama.');
                            }
                        }
                        if (!$bapPrevious && $pertemuan !== 9) {
                            return redirect()->back()->with('error', "Silakan isi BAP pertemuan {$previousPertemuan} terlebih dahulu.");
                        }
                    }
                    $isAllowed = true;
                    break;
                }
            }
            if ($isAllowed) {
                Bap::updateOrCreate(
                    ['schedule_id' => $scheduleId, 'pertemuan' => $pertemuan],
                    [
                        'topik' => $request->input('topik'),
                        'keterangan' => $request->input('keterangan'),
                        'lecturer_id' => $lecturer->id,
                    ]
                );
                return redirect()->route('lecturer.bap.show', $scheduleId)->with('success', 'BAP untuk pertemuan ' . $pertemuan . ' berhasil disimpan.');
            } else {
                return redirect()->back()->with('error', 'Anda tidak berhak mengisi BAP untuk pertemuan ini atau prasyarat belum terpenuhi.');
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('lecturer.bap.index')->with('error', 'Jadwal dengan ID ' . $scheduleId . ' tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Error in BapController@store for schedule_id ' . $scheduleId . ' and pertemuan ' . $pertemuan . ': ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan BAP: ' . $e->getMessage());
        }
    }

    public function printLaporan(Request $request, $id)
    {
        $dosen = auth()->user()->lecturer;

        // Coba ambil jadwal dengan validasi kepemilikan
        $jadwal = Schedule::where('id', $id)
            ->whereHas('lecturersInSchedule', function ($query) use ($dosen) {
                $query->where('lecturer_id', $dosen->id);
            })
            ->with([
                'lecturersInSchedule',
                'kelas',
                'schedulable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Course::class => ['department.faculty'],
                        MkduCourse::class => [], // Tidak ada relasi tambahan yang dimuat untuk MKDU
                    ]);
                },
            ])
            ->first();
        // Validasi kepemilikan
        if (!$jadwal) {
            return redirect()->route('lecturer.bap.index')->with('error', 'Anda tidak berhak mengakses laporan untuk jadwal ini.');
        }

        $attendances = Attendance::where('schedule_id', $id)->with('attendanceDetails')->get();
        $baps = Bap::where('schedule_id', $id)->pluck('pertemuan');
        // Penanganan jika tidak ada data BAP
        if ($baps->isEmpty()) {
            return redirect()->route('lecturer.bap.show', $id)->with('warning', 'Laporan BAP belum bisa dicetak. Silakan isi terlebih dahulu BAP perkuliahan setiap pertemuan.');
        }

        $ta = getCurrentAcademicYear();
        $dateNow = now()->format('d-F-Y');
        $namaLaporan = "Laporan_BAP_Presensi_" . str_replace(" ", "_", $jadwal->course->name) . ".pdf";

        $total = $attendances->count();

        $kehadiran = [];
        foreach ($attendances as $item) {
            $totalHadir = $item->attendanceDetails->where('status', 'Hadir')->count();
            $totalPertemuan = 16;
            $statusPertemuan = [];
            for ($i = 1; $i <= $totalPertemuan; $i++) {
                $detail = $item->attendanceDetails->where('meeting_number', $i)->first();
                $statusPertemuan[$i] = $detail ? $detail->status : 'Tanpa Keterangan';
            }
            $persentase = ($totalHadir / $totalPertemuan) * 100;
            $kehadiran[] = [
                'student' => $item->student,
                'totalHadir' => $totalHadir,
                'statusPertemuan' => $statusPertemuan,
                'persentase' => number_format($persentase, 2)
            ];
        }

        $hadir = collect(range(1, 16))->mapWithKeys(function ($i) use ($attendances) {
            return [$i => $attendances->flatMap->attendanceDetails->where('meeting_number', $i)->where('status', 'Hadir')->count()];
        });

        $signatures = [
            [
                'jabatan' => 'Dosen Pertama',
                'nama' => $jadwal->lecturersInSchedule[0]->nama_dosen ?? '',
                'nip' => $jadwal->lecturersInSchedule[0]->nidn ?? ''
            ],
            [
                'jabatan' => 'Dosen kedua',
                'nama' => $jadwal->lecturersInSchedule[1]->nama_dosen ?? '',
                'nip' => $jadwal->lecturersInSchedule[1]->nidn ?? ''
            ],
            [
                'jabatan' => 'Dekan',
                'nama' => $jadwal->course->department->faculty->dekan ?? '',
                'nip' => $jadwal->course->department->faculty->nip ?? ''
            ],
            [
                'jabatan' => 'Ketua Program Studi',
                'nama' => $jadwal->course->department->kaprodi ?? '',
                'nip' => $jadwal->course->department->nip ?? ''
            ]
        ];
        
        foreach ($signatures as &$sig) {
            $dataQr = $sig['jabatan'] . ' - ' . $sig['nama'] . ' - ' . ($sig['nip'] ?? '') . ' - ' . now()->format('d-m-Y');
            $renderer = new ImageRenderer(
                new RendererStyle(80),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $sig['qrcode'] = 'data:image/svg+xml;base64,' . base64_encode($writer->writeString($dataQr));
        }
        unset($sig);

        $pdfView = view('dosen.bap.cetak', compact('jadwal', 'attendances', 'baps', 'ta', 'dateNow', 'total', 'hadir', 'kehadiran', 'signatures'))->render();
        $pdf = Pdf::loadHTML($pdfView)
            ->setPaper('a4', 'landscape')
            ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        return $pdf->stream($namaLaporan);

    }

}