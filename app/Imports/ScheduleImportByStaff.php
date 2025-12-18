<?php

namespace App\Imports;

use App\Models\Room;
use App\Models\Kelas;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Schedule;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\Rule;

class ScheduleImportByStaff implements ToModel, WithHeadingRow, WithValidation
{
    protected $departmentId;
    protected $academicYearId;
    protected $staff;
    protected $rowCount = 0;

    public function __construct()
    {
        $this->staff = Auth::user()->employee;

        if (!$this->staff || !$this->staff->department) {
            throw new \Exception("Akun staff tidak valid atau tidak memiliki program studi");
        }

        $this->departmentId = $this->staff->department_id;
        $academicYear = AcademicYear::where('status', 1)->first();

        if (!$academicYear) {
            throw new \Exception("Tidak ada tahun akademik aktif");
        }

        $this->academicYearId = $academicYear->id;
    }

    public function rules(): array
    {
        return [
            '*.course_code' => [
                'required',
                Rule::exists('courses', 'code')->where(function ($query) {
                    $query->where('department_id', $this->departmentId);
                })
            ],
            '*.kelas_name' => [
                'required',
                Rule::exists('kelas', 'name')->where(function ($query) {
                    $query->where('department_id', $this->departmentId);
                })
            ],
            '*.room_name' => 'required|exists:rooms,name',
            //Dosen pertama
            '*.lecturer1_nidn' => [
                'required',
                Rule::exists('lecturers', 'nidn')->where(function ($query) {
                    $query->where('department_id', $this->departmentId);
                })
            ],
            '*.lecturer1_start' => 'required|integer|min:1|max:8',
            '*.lecturer1_end' => 'required|integer|gte:*.lecturer1_start',
            // Dosen kedua
            '*.lecturer2_nidn' => [
                'required',
                Rule::exists('lecturers', 'nidn')->where(function ($query) {
                    $query->where('department_id', $this->departmentId);
                })
            ],
            '*.lecturer2_start' => 'required|integer|min:9|max:16',
            '*.lecturer2_end' => 'required|integer|gte:*.lecturer2_start',

        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.course_code.exists' => 'Kode mata kuliah :input tidak ditemukan atau tidak termasuk di Program Studi Anda',
            '*.kelas_name.exists' => 'Nama kelas :input tidak ditemukan atau tidak termasuk di Program Studi Anda',
            '*.lecturer1_nidn.exists' => 'NIDN dosen 1 :input tidak ditemukan atau tidak termasuk di Program Studi Anda',
            '*.lecturer2_nidn.exists' => 'NIDN dosen 2 :input tidak ditemukan atau tidak termasuk di Program Studi Anda',
        ];
    }

    public function model(array $row)
    {
        $row = array_map('trim', $row);
        // Validasi data dasar
        $course = Course::where('code', $row['course_code'])->firstOrFail();
        $kelas = Kelas::where('name', $row['kelas_name'])->firstOrFail();
        $room = Room::where('name', $row['room_name'])->firstOrFail();

        $exists = Schedule::where([
            'course_id' => $course->id,
            'kelas_id' => $kelas->id,
            'hari' => $row['hari'],
            'waktu' => $row['waktu']
        ])->exists();

        if ($exists) {
            throw new \Exception("Jadwal untuk mata kuliah {$row['course_code']} sudah ada");
        }

        // Buat schedule
        $schedule = Schedule::create([
            'department_id' => $this->staff->department_id,
            'academic_year_id' => $this->academicYearId,
            'course_id' => $course->id,
            'kelas_id' => $kelas->id,
            'room_id' => $room->id,
            'hari' => $row['hari'],
            'waktu' => $row['waktu']
        ]);

        // Proses dosen pertama (wajib)
        $this->processLecturer(
            $schedule,
            $row['lecturer1_nidn'],
            $row['lecturer1_start'],
            $row['lecturer1_end']
        );

        // Proses Dosen kedua
        if (!empty($row['lecturer2_nidn']) && !empty($row['lecturer2_start']) && !empty($row['lecturer2_end'])) {
            $this->processLecturer(
                $schedule,
                $row['lecturer2_nidn'],
                $row['lecturer2_start'],
                $row['lecturer2_end']
            );
        }

        $this->rowCount++;
        return $schedule;
    }

    protected function processLecturer($schedule, $nidn, $start, $end)
    {
        $lecturer = Lecturer::where('nidn', $nidn)->firstOrFail();
        // Pivot Tabel lecturer_schedule
        $schedule->lecturersInSchedule()->attach($lecturer->id, [
            'start_pertemuan' => $start,
            'end_pertemuan' => $end
        ]);

        // Gunakan syncWithoutDetaching untuk menghindari duplikasi data di pivot table
        $schedule->lecturersInSchedule()->syncWithoutDetaching([
            $lecturer->id => [
                'start_pertemuan' => $start,
                'end_pertemuan' => $end
            ]
        ]);
    }

    public function getRowCount()
    {
        return $this->rowCount;
    }
}