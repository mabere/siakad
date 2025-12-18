<?php

namespace App\Http\Controllers\Student;

use App\Models\Schedule;
use App\Models\StudyPlan;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class StudyPlanController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            if (!$user || !$user->hasRole('mahasiswa')) {
                return redirect()->route('login')->with('error', 'Silakan login sebagai mahasiswa');
            }
            $user->loadMissing('student.department');
            $student = $user->student;
            if (!$student) {
                return redirect()->route('login')->with('error', 'Data mahasiswa tidak ditemukan');
            }
            $ta = getCurrentAcademicYear();
            if (!$ta) {
                return back()->with('error', 'Tidak ada tahun akademik yang aktif. Silakan hubungi admin.');
            }
            $isKrsActive = $ta->isKrsPeriodActive();
            $currentAcademicYearStudyPlans = StudyPlan::where('student_id', $student->id)
                ->where('academic_year_id', $ta->id)
                ->whereIn('status', ['pending', 'approved'])
                ->with([
                    'schedule.schedulable',
                    'schedule.kelas',
                    'schedule.lecturersInSchedule',
                    'mkduCourse',
                ])->get();
            $existingScheduleIds = $currentAcademicYearStudyPlans->pluck('schedule_id')->filter()->all();
            $schedules = Schedule::where('academic_year_id', $ta->id)
                ->where('department_id', $student->department_id)
                ->whereNotIn('id', $existingScheduleIds)
                ->with(['schedulable', 'kelas', 'lecturersInSchedule'])
                ->get();
            $totalSks = $currentAcademicYearStudyPlans->sum(function ($studyPlan) {
                return $studyPlan->sks;
            });
            $krsHistory = StudyPlan::where('student_id', $student->id)
                ->with([
                    'academicYear',
                    'schedule.schedulable',
                    'schedule.kelas',
                    'schedule.lecturersInSchedule',
                    'mkduCourse'
                ])
                ->orderBy('academic_year_id', 'desc')
                ->get()
                ->groupBy('academic_year_id');

            return view('mhs.krs.index', compact(
                'currentAcademicYearStudyPlans',
                'schedules',
                'totalSks',
                'isKrsActive',
                'krsHistory',
                'student',
                'ta'
            ));

        } catch (\Exception $e) {
            Log::error('KRS Index Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Terjadi kesalahan saat memuat halaman KRS. Silakan coba lagi.');
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->hasRole('mahasiswa')) {
                // Gunakan ValidationException untuk konsistensi pesan error
                throw ValidationException::withMessages(['auth_error' => 'Silakan login sebagai mahasiswa.']);
            }

            // Eager load student jika belum dimuat
            if (!$user->relationLoaded('student')) {
                $user->load('student');
            }
            $student = $user->student;
            if (!$student) {
                throw ValidationException::withMessages(['student_error' => 'Data mahasiswa tidak ditemukan.']);
            }

            $ta = getCurrentAcademicYear();
            if (!$ta) {
                throw ValidationException::withMessages(['academic_year_error' => 'Tidak ada tahun akademik yang aktif. Silakan hubungi admin.']);
            }

            // VALIDASI KRITIS: Periode KRS harus aktif
            if (!$ta->isKrsPeriodActive()) {
                throw ValidationException::withMessages(['krs_period_error' => 'Periode pengisian KRS telah berakhir. Anda tidak dapat menambah mata kuliah.']);
            }

            // Validasi input
            $request->validate([
                'selected_schedule_ids' => 'required|array|min:1',
                'selected_schedule_ids.*' => 'exists:schedules,id', // Pastikan ID jadwal valid
            ], [
                'selected_schedule_ids.required' => 'Anda harus memilih setidaknya satu mata kuliah.',
                'selected_schedule_ids.min' => 'Anda harus memilih setidaknya satu mata kuliah.',
                'selected_schedule_ids.*.exists' => 'Salah satu mata kuliah yang Anda pilih tidak valid.',
            ]);

            $selectedScheduleIds = $request->input('selected_schedule_ids');

            DB::beginTransaction();

            // Ambil jadwal yang dipilih beserta relasinya
            // Gunakan 'schedulable' untuk Course atau MkduCourse
            $selectedSchedules = Schedule::whereIn('id', $selectedScheduleIds)
                ->with(['schedulable', 'lecturersInSchedule']) // Eager load schedulable dan dosen
                ->get();

            if ($selectedSchedules->isEmpty()) {
                throw ValidationException::withMessages(['selection_error' => 'Tidak ada mata kuliah yang valid ditemukan dari pilihan Anda.']);
            }

            // Dapatkan mata kuliah yang sudah diambil (pending atau approved) untuk TA ini
            $existingStudyPlans = StudyPlan::where('student_id', $student->id)
                ->where('academic_year_id', $ta->id)
                ->whereIn('status', ['pending', 'approved'])
                ->with(['schedule.schedulable']) // Eager load schedule dan schedulable-nya untuk pengecekan bentrok
                ->get();

            // Hitung total SKS saat ini
            $currentTotalSks = $existingStudyPlans->sum(function ($studyPlan) {
                return $studyPlan->sks; // Menggunakan accessor 'sks' dari StudyPlan yang sudah Anda buat
            });

            $maxSks = config('academic.max_sks', 24);
            $sksToAdd = 0;
            $krsEntriesToCreate = [];
            $allCurrentAndNewScheduleTimes = []; // Untuk deteksi bentrok yang komprehensif
            $errors = [];

            // Tambahkan jadwal yang sudah ada ke daftar untuk pengecekan bentrok
            foreach ($existingStudyPlans as $existingPlan) {
                if ($existingPlan->schedule) { // Pastikan ada jadwal, karena MKDU bisa tanpa jadwal di StudyPlan
                    $allCurrentAndNewScheduleTimes[] = [
                        'id' => $existingPlan->schedule->id,
                        'name' => $existingPlan->course->name ?? 'Mata Kuliah Tidak Diketahui',
                        'hari' => $existingPlan->schedule->hari,
                        'start_time' => $existingPlan->schedule->start_time,
                        'end_time' => $existingPlan->schedule->end_time,
                    ];
                }
            }

            foreach ($selectedSchedules as $schedule) {
                // Mencegah duplikasi mata kuliah yang baru dipilih atau yang sudah ada di KRS
                $isDuplicate = false;
                if ($schedule->schedulable_type === 'App\\Models\\MkduCourse') {
                    // Jika ini MKDU, cek berdasarkan mkdu_course_id di StudyPlan (jika ada)
                    // Atau schedule_id jika MKDU terikat jadwal
                    $isDuplicate = $existingStudyPlans->contains(function ($plan) use ($schedule) {
                        return ($plan->mkdu_course_id === $schedule->schedulable_id) || ($plan->schedule_id === $schedule->id);
                    });
                } else {
                    // Jika ini Course reguler, cek berdasarkan schedule_id
                    $isDuplicate = $existingStudyPlans->contains('schedule_id', $schedule->id);
                }

                if ($isDuplicate) {
                    $errors[] = 'Mata kuliah "' . ($schedule->course_name ?? 'N/A') . '" sudah ada di KRS Anda.';
                    continue; // Lewati mata kuliah ini jika duplikat
                }

                $sksToAdd += $schedule->sks_value;

                // Persiapkan slot jadwal baru untuk pengecekan bentrok
                $newSlot = [
                    'id' => $schedule->id,
                    'name' => $schedule->course_name,
                    'hari' => $schedule->hari,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                ];

                // Deteksi Bentrok Jadwal
                foreach ($allCurrentAndNewScheduleTimes as $existingSlot) {
                    // Pastikan tidak membandingkan dengan dirinya sendiri (jika ID jadwal sama)
                    if ($existingSlot['id'] === $newSlot['id']) {
                        continue;
                    }

                    if ($newSlot['hari'] === $existingSlot['hari']) {
                        // Cek overlap waktu
                        $overlap = (
                            ($newSlot['start_time'] < $existingSlot['end_time'] && $newSlot['end_time'] > $existingSlot['start_time']) ||
                            ($existingSlot['start_time'] < $newSlot['end_time'] && $existingSlot['end_time'] > $newSlot['start_time']) ||
                            ($newSlot['start_time'] >= $existingSlot['start_time'] && $newSlot['end_time'] <= $existingSlot['end_time']) ||
                            ($existingSlot['start_time'] >= $newSlot['start_time'] && $existingSlot['end_time'] <= $newSlot['end_time'])
                        );

                        if ($overlap) {
                            $errors[] = 'Jadwal mata kuliah "' . $newSlot['name'] . '" bentrok dengan "' . $existingSlot['name'] . '" pada hari ' . $newSlot['hari'] . ' (' . $newSlot['start_time']->format('H:i') . '-' . $newSlot['end_time']->format('H:i') . ' vs ' . $existingSlot['start_time']->format('H:i') . '-' . $existingSlot['end_time']->format('H:i') . ').';
                            continue 2; // Lompati ke jadwal berikutnya
                        }
                    }
                }

                // Jika tidak ada bentrok, tambahkan ke daftar jadwal yang akan di-check bentrok di iterasi selanjutnya
                $allCurrentAndNewScheduleTimes[] = $newSlot;

                // Siapkan data untuk entri KRS baru
                $krsEntriesToCreate[] = [
                    'student_id' => $student->id,
                    'academic_year_id' => $ta->id,
                    // Pastikan penentuan schedule_id dan mkdu_course_id benar
                    // Berdasarkan model Schedule Anda, mkdu_course_id adalah kolom langsung.
                    // Jika schedulable_type adalah MkduCourse, gunakan schedulable_id sebagai mkdu_course_id,
                    // dan schedule_id adalah ID dari record Schedule itu sendiri.
                    'schedule_id' => $schedule->id, // schedule_id selalu diisi dengan ID jadwal yang dipilih
                    // 'mkdu_course_id' => ($schedule->schedulable_type === 'App\\Models\\MkduCourse') ? $schedule->schedulable_id : null,
                    'status' => 'pending',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            // Jika ada error (duplikasi/bentrok), batalkan transaksi dan kembalikan error
            if (!empty($errors)) {
                DB::rollBack();
                // Menggabungkan semua error menjadi satu pesan atau mengirimkannya sebagai array
                throw ValidationException::withMessages(['krs_errors' => $errors]);
            }

            // Validasi total SKS akhir
            if (($currentTotalSks + $sksToAdd) > $maxSks) {
                DB::rollBack();
                throw ValidationException::withMessages(['sks_limit' => "Total SKS melebihi batas maksimum $maxSks SKS. Total SKS saat ini: $currentTotalSks, SKS yang akan ditambahkan: $sksToAdd."]);
            }

            // Simpan semua entri KRS baru sekaligus
            if (!empty($krsEntriesToCreate)) {
                StudyPlan::insert($krsEntriesToCreate);
            }

            DB::commit();

            return redirect()->route('student.krs.index')->with('success', 'Mata kuliah berhasil ditambahkan ke KRS Anda.');

        } catch (ValidationException $e) {
            // Jika ada ValidationException, kembalikan dengan error ke view
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            DB::rollBack(); // Pastikan rollback jika ada error tak terduga
            Log::error('KRS Store Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan KRS: ' . $e->getMessage());
        }
    }

    public function destroy(StudyPlan $studyPlan)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->hasRole('mahasiswa')) {
                // Return dengan ValidationException agar konsisten dengan error handling di `store`
                throw ValidationException::withMessages(['auth_error' => 'Silakan login sebagai mahasiswa.']);
            }

            // Pastikan relasi 'student' dimuat pada user
            // Ini sangat penting untuk memastikan $user->student tersedia
            if (!$user->relationLoaded('student')) {
                $user->load('student');
            }

            $student = $user->student;
            if (!$student) {
                // Jika data student tidak ditemukan setelah load, lemparkan exception
                throw ValidationException::withMessages(['student_error' => 'Data mahasiswa tidak ditemukan untuk pengguna ini.']);
            }

            // Validasi: Pastikan StudyPlan yang akan dihapus adalah milik mahasiswa yang sedang login
            if ($studyPlan->student_id !== $student->id) {
                throw ValidationException::withMessages(['authorization_error' => 'Anda tidak berhak menghapus mata kuliah ini. Ini bukan mata kuliah di KRS Anda.']);
            }

            $ta = getCurrentAcademicYear();
            if (!$ta) {
                throw ValidationException::withMessages(['academic_year_error' => 'Tidak ada tahun akademik yang aktif. Silakan hubungi admin.']);
            }

            // Validasi: Periode KRS harus aktif untuk menghapus
            if (!$ta->isKrsPeriodActive()) {
                throw ValidationException::withMessages(['krs_period_error' => 'Periode pengisian KRS telah berakhir. Anda tidak dapat menghapus mata kuliah.']);
            }

            // Validasi: Hanya status 'pending' yang boleh dihapus
            if ($studyPlan->status !== 'pending') {
                throw ValidationException::withMessages(['status_error' => 'Mata kuliah dengan status "' . ucfirst($studyPlan->status) . '" tidak dapat dihapus. Hanya status "Pending" yang bisa dihapus.']);
            }

            DB::beginTransaction();

            // Ambil nama mata kuliah sebelum dihapus menggunakan accessor `course` di StudyPlan
            // Accessor ini akan mencari dari schedule->course atau mkduCourse
            $studyPlanName = $studyPlan->course->name ?? 'Mata Kuliah Tidak Diketahui';

            // Hapus StudyPlan (soft delete karena ada use SoftDeletes di model StudyPlan)
            $studyPlan->delete();

            DB::commit();

            return redirect()->route('student.krs.index')->with('success', 'Mata kuliah "' . $studyPlanName . '" berhasil dihapus dari KRS Anda.');

        } catch (ValidationException $e) {
            // Gunakan `withErrors` untuk mengirim pesan validasi ke view
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KRS Destroy Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menghapus mata kuliah: ' . $e->getMessage());
        }
    }

    public function jadwal()
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->hasRole('mahasiswa')) {
                return redirect()->route('login')->with('error', 'Silakan login sebagai mahasiswa.');
            }

            // Pastikan relasi 'student' dimuat pada user
            $user->loadMissing('student');
            $student = $user->student;

            if (!$student) {
                return redirect()->route('login')->with('error', 'Data mahasiswa tidak ditemukan.');
            }

            $ta = getCurrentAcademicYear();
            if (!$ta) {
                return back()->with('error', 'Tidak ada tahun akademik yang aktif. Silakan hubungi admin.');
            }

            // Ambil semua StudyPlan mahasiswa untuk tahun akademik AKTIF
            // Relasi 'schedule.schedulable' akan memuat Course atau MkduCourse sesuai type-nya
            $studyPlans = StudyPlan::where('student_id', $student->id)
                ->where('academic_year_id', $ta->id)
                ->whereIn('status', ['approved', 'pending'])
                ->with([
                    'schedule.schedulable', // Ini yang penting untuk Course atau MkduCourse
                    'schedule.room',
                    'schedule.kelas',
                    'schedule.lecturersInSchedule', // Relasi ke Dosen melalui pivot
                    'mkduCourse', // Untuk MKDU yang tidak punya jadwal (langsung di study_plans)
                    'mkduCourse.room', // Jika mkduCourse punya room langsung
                    'mkduCourse.lecturers', // Jika mkduCourse punya lecturers langsung
                ])
                ->get();

            $groupedSchedules = collect();
            $mkduCoursesWithoutSchedule = collect(); // Untuk MKDU yang *hanya* ada di StudyPlan tanpa Schedule

            foreach ($studyPlans as $studyPlan) {
                if ($studyPlan->schedule) {
                    // Ini adalah StudyPlan yang terhubung ke Schedule (bisa Course atau MkduCourse)
                    $schedule = $studyPlan->schedule;
                    $schedule->study_plan_status = $studyPlan->status;

                    // Tambahkan atribut `is_mkdu` untuk memudahkan penanganan di Blade
                    $schedule->is_mkdu = ($schedule->schedulable_type === 'App\\Models\\MkduCourse');

                    // SKS dari Schedule harus diambil dari schedulable-nya
                    $schedule->sks_value = $schedule->schedulable->sks ?? 0;

                    // Mengakses nama mata kuliah dari schedulable
                    $schedule->currentCourse = $schedule->schedulable;

                    if ($schedule->hari) {
                        // Jika ada hari, masukkan ke jadwal berdasarkan hari
                        if (!$groupedSchedules->has($schedule->hari)) {
                            $groupedSchedules->put($schedule->hari, collect());
                        }
                        $groupedSchedules->get($schedule->hari)->push($schedule);
                    } else {
                        // Ini adalah jadwal (Schedule) yang tidak punya hari (mungkin MKDU yang "fleksibel")
                        // Kita bisa tetap menambahkannya ke kategori 'Mata Kuliah Umum'
                        Log::warning('Jadwal (Schedule) tanpa hari ditemukan: ID ' . $schedule->id);
                        $mkduCoursesWithoutSchedule->push($schedule);
                    }

                } elseif ($studyPlan->mkduCourse) {
                    // Ini adalah StudyPlan yang *hanya* terhubung ke MkduCourse, tanpa Schedule
                    // Artinya, MKDU ini tidak memiliki jadwal spesifik (hari, waktu, ruang) di tabel schedules
                    $mkduCourse = $studyPlan->mkduCourse;

                    // Buat objek pseudo-schedule agar konsisten dengan struktur data jadwal
                    $mkduSchedule = (object) [
                        'id' => 'mkdu-' . $studyPlan->id, // ID unik untuk identifikasi di Blade
                        'is_mkdu' => true,
                        'currentCourse' => $mkduCourse, // Mata kuliah MKDU itu sendiri
                        'sks_value' => $mkduCourse->sks ?? 0,
                        'start_time' => null, // Tidak ada waktu spesifik
                        'end_time' => null,   // Tidak ada waktu spesifik
                        'hari' => null,       // Tidak ada hari spesifik
                        'room' => $mkduCourse->room ?? (object) ['name' => 'Online/TBA'],
                        'study_plan_status' => $studyPlan->status,
                        'kelas' => (object) ['name' => 'MKDU'], // Label untuk MKDU
                        'lecturersInSchedule' => $mkduCourse->lecturers, // Dosen MKDU
                        'lecturer_schedule_details' => $mkduCourse->lecturers, // Untuk Blade jika perlu pivot detail
                    ];
                    $mkduCoursesWithoutSchedule->push($mkduSchedule);
                }
            }

            // Urutkan jadwal per hari berdasarkan waktu mulai
            $groupedSchedules = $groupedSchedules->map(function ($daySchedules) {
                return $daySchedules->sortBy(function ($schedule) {
                    return $schedule->start_time ?? Carbon::maxValue(); // Urutkan berdasarkan start_time, null di akhir
                })->values();
            });

            // Urutan hari untuk tampilan
            $orderedDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            $finalSchedules = collect();

            foreach ($orderedDays as $day) {
                if ($groupedSchedules->has($day)) {
                    $finalSchedules->put($day, $groupedSchedules->get($day));
                }
            }

            // Tambahkan MKDU yang tidak punya jadwal ke kategori "Mata Kuliah Umum"
            if ($mkduCoursesWithoutSchedule->isNotEmpty()) {
                $finalSchedules->put('Mata Kuliah Umum', $mkduCoursesWithoutSchedule);
            }

            // Hitung total SKS
            $totalSks = $studyPlans->sum(function ($studyPlan) {
                if ($studyPlan->schedule) {
                    // Jika ada jadwal, SKS diambil dari schedule->schedulable
                    return $studyPlan->schedule->schedulable->sks ?? 0;
                } elseif ($studyPlan->mkduCourse) {
                    // Jika hanya MKDU, SKS diambil dari mkduCourse langsung
                    return $studyPlan->mkduCourse->sks ?? 0;
                }
                return 0;
            });

            return view('mhs.jadwal.index', [
                'schedules' => $finalSchedules,
                'ta' => $ta,
                'totalSks' => $totalSks,
            ]);

        } catch (\Exception $e) {
            Log::error('Jadwal Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Terjadi kesalahan saat memuat jadwal: ' . $e->getMessage());
        }
    }

    public function print(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->hasRole('mahasiswa')) {
                return redirect()->route('login')->with('error', 'Silakan login sebagai mahasiswa');
            }
            $user->load([
                'student' => function ($query) {
                    $query->with([
                        'department.faculty',
                        'advisor',
                        'kelas.lecturer'
                    ]);
                }
            ]);

            $student = $user->student;
            if (!$student) {
                return redirect()->route('login')->with('error', 'Data mahasiswa tidak ditemukan');
            }

            $academicYearId = $request->query('academic_year_id');
            $ta = $academicYearId
                ? AcademicYear::findOrFail($academicYearId)
                : AcademicYear::where('status', 1)->first();

            if (!$ta) {
                return back()->with('error', 'Tahun akademik tidak ditemukan');
            }

            // Ambil tanggal sekarang
            $tgl = now()->translatedFormat('d F Y');
            $items = StudyPlan::with([
                'schedule.course',
                'schedule.lecturersInSchedule',
                'schedule.kelas',
                'mkduCourse',
                'approvedBy',
            ])
                ->where('student_id', $student->id)
                ->where('academic_year_id', $ta->id) // Akses sebagai objek
                ->whereIn('status', ['approved', 'pending'])
                ->get();

            $totalSks = $items->sum(function ($item) {
                return $item->schedule->course->sks ?? $item->mkduCourse->sks ?? 0; // Akses sebagai objek
            });

            $pdf = Pdf::loadView('mhs.krs.cetak', [
                'mahasiswa' => $student,
                'ta' => $ta,
                'items' => $items,
                'totalSks' => $totalSks,
                'tgl' => $tgl,
            ]);

            $pdf->setPaper('a4', 'portrait');
            return $pdf->stream("KRS_{$student->nim}_{$ta->ta}.pdf");

        } catch (\Exception $e) {
            Log::error('KRS Print Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mencetak KRS: ' . $e->getMessage());
        }
    }

}