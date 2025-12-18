<?php

namespace App\Http\Controllers\Staff;


use App\Models\Room;
use App\Models\Kelas;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Schedule;
use App\Models\Department;
use App\Models\MkduCourse;
use Illuminate\Http\Request;
use App\Models\LecturerSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ScheduleImportByStaff;
use Maatwebsite\Excel\Validators\ValidationException;


class JadwalKuliahByStaffController extends Controller
{
    public function index()
    {
        $staff = Auth::user()->employee;

        if (!$staff || !$staff->department) {
            return redirect()->route('dashboard')->with('error', 'Data departemen tidak ditemukan.');
        }

        $ta = getCurrentAcademicYear();
        $items = Schedule::where('academic_year_id', $ta->id)
            ->where('department_id', $staff->department_id)
            ->with(['course', 'mkduCourse', 'room', 'lecturersInSchedule'])
            ->get();
        $prodi = Department::all();
        return view('staff.jadwal.index', compact('ta', 'prodi', 'items'));
    }

    public function create()
    {
        $staff = Auth::user()->employee;

        if (!$staff || !$staff->department) {
            return redirect()->route('dashboard')->with('error', 'Program Studi tidak ditemukan.');
        }

        $ta = getCurrentAcademicYear();
        $id = $staff->department_id;
        $matkul = Course::where('department_id', $staff->department_id)->get();
        $dosen = Lecturer::where('department_id', $staff->department_id)->get();
        $kelas = Kelas::where('department_id', $staff->department_id)->get();
        $ruangan = Room::all();

        return view('staff.jadwal.create', compact('id', 'ta', 'matkul', 'dosen', 'kelas', 'ruangan'));
    }

    public function store(Request $request)
    {
        $staff = Auth::user()->employee;

        if (!$staff || !$staff->department_id) {
            return redirect()->route('dashboard')->with('error', 'Data pegawai atau departemen tidak ditemukan.');
        }

        $validated = $request->validate([
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'course_id' => 'required|exists:courses,id',
            'lecturer1_id' => 'required|exists:lecturers,id',
            'lecturer1_start' => 'required|integer|min:1|max:16',
            'lecturer1_end' => 'required|integer|min:1|max:16|gte:lecturer1_start',
            'lecturer2_id' => 'nullable|different:lecturer1_id|exists:lecturers,id',
            'lecturer2_start' => 'nullable|integer|min:1|max:16',
            'lecturer2_end' => 'nullable|integer|min:1|max:16|gte:lecturer2_start',
            'kelas_id' => 'required|exists:kelas,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room_id' => 'required|exists:rooms,id',
        ]);

        try {
            // Simpan jadwal
            $schedule = Schedule::create([
                'department_id' => $staff->department_id,
                'academic_year_id' => $validated['academic_year_id'],
                'kelas_id' => $validated['kelas_id'],
                'hari' => $validated['hari'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'room_id' => $validated['room_id'],
                'schedulable_id' => $validated['course_id'],
                'schedulable_type' => Course::class,
            ]);

            // Tambah dosen 1
            LecturerSchedule::create([
                'schedule_id' => $schedule->id,
                'lecturer_id' => $validated['lecturer1_id'],
                'start_pertemuan' => $validated['lecturer1_start'],
                'end_pertemuan' => $validated['lecturer1_end'],
            ]);

            // Tambah dosen 2 jika ada
            if (!empty($validated['lecturer2_id'])) {
                LecturerSchedule::create([
                    'schedule_id' => $schedule->id,
                    'lecturer_id' => $validated['lecturer2_id'],
                    'start_pertemuan' => $validated['lecturer2_start'],
                    'end_pertemuan' => $validated['lecturer2_end'],
                ]);
            }

            return redirect()->route('staff.jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Gagal simpan jadwal oleh staff: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menyimpan jadwal: ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
    }

    public function edits(string $id)
    {
        $staff = Auth::user()->employee;

        if (!$staff || !$staff->department) {
            return redirect()->route('dashboard')->with('error', 'Program Studi tidak ditemukan.');
        }

        // Load jadwal dengan relasi
        $schedule = Schedule::with(['schedulable', 'room', 'lecturersInSchedule'])->findOrFail($id);

        // Ambil tahun akademik aktif
        $ta = getCurrentAcademicYear();
        if (!$ta) {
            return redirect()->back()->with('error', 'Tahun akademik aktif tidak ditemukan.');
        }

        // Tentukan semester ganjil/genap
        $semesterNumbers = $ta->semester === 'Ganjil' ? [1, 3, 5, 7] : [2, 4, 6, 8];

        // Ambil data matkul prodi dan mkdu dengan kolom terbatas
        $matkul = Course::where('department_id', $staff->department_id)
            ->whereIn('semester_number', $semesterNumbers)
            ->select('id', 'name', 'semester_number')
            ->orderBy('semester_number')
            ->get();

        $mkduCourses = MkduCourse::whereIn('semester_number', $semesterNumbers)
            ->select('id', 'name', 'semester_number')
            ->orderBy('semester_number')
            ->get();

        // Ambil dosen, kelas, dan ruang dengan filter
        $dosen = Lecturer::where('department_id', $staff->department_id)
            ->select('id', 'nama_dosen')
            ->get();
        $kelas = Kelas::where('department_id', $staff->department_id)
            ->select('id', 'name')
            ->get();
        $ruangan = Room::all();

        // Proses data dosen dari relasi
        $lecturers = $schedule->lecturersInSchedule->map(function ($item) {
            return [
                'id' => $item->lecturer_id,
                'start' => $item->start_pertemuan,
                'end' => $item->end_pertemuan,
            ];
        })->values()->toArray();

        // Pastikan lecturer1 dan lecturer2 memiliki nilai default yang konsisten
        $lecturer1 = $lecturers[0] ?? ['id' => '', 'start' => '', 'end' => ''];
        $lecturer2 = $lecturers[1] ?? ['id' => '', 'start' => '', 'end' => ''];

        return view('staff.jadwal.edit', compact(
            'schedule',
            'ta',
            'matkul',
            'mkduCourses',
            'dosen',
            'kelas',
            'ruangan',
            'lecturer1',
            'lecturer2'
        ));
    }

    public function edit(string $id)
    {
        $staff = Auth::user()->employee;

        if (!$staff || !$staff->department) {
            return redirect()->route('dashboard')->with('error', 'Program Studi tidak ditemukan.');
        }
        $schedule = Schedule::with(['schedulable', 'room', 'lecturersInSchedule'])
            ->findOrFail($id);
        $ta = getCurrentAcademicYear();
        if (!$ta) {
            return redirect()->back()->with('error', 'Tahun akademik aktif tidak ditemukan.');
        }
        $semesterNumbers = $ta->semester === 'Ganjil' ? [1, 3, 5, 7] : [2, 4, 6, 8];
        $matkul = Course::where('department_id', $staff->department_id)
            ->whereIn('semester_number', $semesterNumbers)
            ->select('id', 'name', 'semester_number')
            ->orderBy('semester_number')
            ->get();
        $mkduCourses = MkduCourse::whereIn('semester_number', $semesterNumbers)
            ->select('id', 'name', 'semester_number')
            ->orderBy('semester_number')
            ->get();

        $dosen = Lecturer::where('department_id', $staff->department_id)
            ->select('id', 'nama_dosen')
            ->get();
        $kelas = Kelas::where('department_id', $staff->department_id)
            ->select('id', 'name')
            ->get();
        $ruangan = Room::all();
        $lecturers = $schedule->lecturersInSchedule->map(function ($item, $index) {
            return [
                'id' => (string) ($item->pivot->lecturer_id ?? ''),
                'start' => (string) ($item->pivot->start_pertemuan ?? ''),
                'end' => (string) ($item->pivot->end_pertemuan ?? '')
            ];
        })->values()->toArray();
        $lecturer1 = $lecturers[0] ?? ['id' => '', 'start' => '', 'end' => ''];
        $lecturer2 = $lecturers[1] ?? ['id' => '', 'start' => '', 'end' => ''];
        return view('staff.jadwal.edit', compact(
            'schedule',
            'ta',
            'matkul',
            'mkduCourses',
            'dosen',
            'kelas',
            'ruangan',
            'lecturer1',
            'lecturer2'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'academic_year_id' => 'required|integer',
            'schedulable_type' => 'required|string|in:App\Models\Course,App\Models\MkduCourse',
            'schedulable_id' => 'required|integer',
            'lecturer1_id' => 'required|integer',
            'lecturer1_start' => 'required|integer|lt:lecturer1_end',
            'lecturer1_end' => 'required|integer',
            'lecturer2_id' => 'nullable|integer',
            'lecturer2_start' => 'nullable|integer|lt:lecturer2_end',
            'lecturer2_end' => 'nullable|integer',
            'kelas_id' => 'required|integer',
            'hari' => 'required|string',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'room_id' => 'required|integer',
        ]);

        $schedule = Schedule::findOrFail($id);
        $courseModel = $request->input('schedulable_type');
        $course = $courseModel::findOrFail($request->input('schedulable_id'));

        DB::transaction(function () use ($request, $schedule, $course) {
            // Update jadwal
            $schedule->update([
                'academic_year_id' => $request->academic_year_id,
                'schedulable_type' => $request->schedulable_type,
                'schedulable_id' => $request->schedulable_id,
                'kelas_id' => $request->kelas_id,
                'hari' => $request->hari,
                'room_id' => $request->room_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
            ]);

            // Update relasi polimorfik
            $schedule->schedulable()->associate($course);

            // Update dosen
            $lecturers = [
                [
                    'id' => $request->lecturer1_id,
                    'start' => $request->lecturer1_start,
                    'end' => $request->lecturer1_end,
                ],
                [
                    'id' => $request->lecturer2_id,
                    'start' => $request->lecturer2_start,
                    'end' => $request->lecturer2_end,
                ],
            ];

            $schedule->lecturersInSchedule()->sync([]);

            foreach ($lecturers as $lecturer) {
                if ($lecturer['id']) {
                    LecturerSchedule::updateOrCreate(
                        ['schedule_id' => $schedule->id, 'lecturer_id' => $lecturer['id']],
                        ['start_pertemuan' => $lecturer['start'], 'end_pertemuan' => $lecturer['end']]
                    );
                }
            }
        });

        return redirect()->route('staff.jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $staff = Auth::user()->employee;
        if (!$staff || !$staff->department) {
            return redirect()->route('dashboard')->with('error', 'Program Studi tidak ditemukan.');
        }
        $schedule = Schedule::where('id', $id)
            ->where('department_id', $staff->department_id)
            ->firstOrFail();
        $schedule->delete();
        return redirect()->route('staff.jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }

    public function import(Request $request)
    {
        try {
            Excel::import(new ScheduleImportByStaff, $request->file('file'));
            return back()->with('success', 'Import jadwal berhasil!');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                foreach ($failure->errors() as $error) {
                    $errorMessages[] = "Baris " . $failure->row() . ": " . $error;
                }
            }
            return back()->with('error', implode('<br>', $errorMessages));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}