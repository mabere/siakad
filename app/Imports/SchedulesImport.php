<?php

namespace App\Imports;

use App\Models\Room;
use App\Models\Kelas;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Schedule;
use App\Models\MkduCourse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SchedulesImport implements ToCollection, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    protected $departmentId;
    protected $academicYearId;
    protected $errors = [];

    public function __construct($departmentId, $academicYearId)
    {
        $this->departmentId = $departmentId;
        $this->academicYearId = $academicYearId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $rowIndex => $row) {
            if (!$row->has('rowNumber')) {
                $rows[$rowIndex]->put('rowNumber', $rowIndex + 2);
            }
        }

        foreach ($rows as $row) {
            $rowNumber = $row->get('rowNumber', 'Unknown');

            Log::debug("Processing row: " . $rowNumber, $row->toArray());

            try {
                // Trim semua nilai string untuk menghilangkan spasi ekstra
                $row = $row->map(function ($value) {
                    return is_string($value) ? trim($value) : $value;
                });

                $isMkdu = strtolower($row['tipe_mata_kuliah']) === 'mkdu';
                $schedulableId = null;
                $schedulableType = null;

                if ($isMkdu) {
                    Log::debug("Searching MKDU Course with code: " . $row['kode_mata_kuliah']);
                    $mkduCourse = MkduCourse::where('code', $row['kode_mata_kuliah'])->first();
                    if (!$mkduCourse) {
                        $errorMsg = "Baris " . $rowNumber . ": Kode Mata Kuliah MKDU '{$row['kode_mata_kuliah']}' tidak ditemukan.";
                        $this->errors[] = $errorMsg;
                        Log::warning($errorMsg);
                        continue;
                    }
                    $schedulableId = $mkduCourse->id;
                    $schedulableType = MkduCourse::class;
                    Log::debug("Found MKDU Course: " . $mkduCourse->name . " (ID: " . $mkduCourse->id . ")");
                } else {
                    Log::debug("Searching Prodi Course with code: " . $row['kode_mata_kuliah'] . " and department_id: " . $this->departmentId);
                    $course = Course::where('code', $row['kode_mata_kuliah'])
                        ->where('department_id', $this->departmentId)
                        ->first();
                    if (!$course) {
                        $errorMsg = "Baris " . $rowNumber . ": Kode Mata Kuliah Prodi '{$row['kode_mata_kuliah']}' tidak ditemukan untuk prodi ini.";
                        $this->errors[] = $errorMsg;
                        Log::warning($errorMsg);
                        continue;
                    }
                    $schedulableId = $course->id;
                    $schedulableType = Course::class;
                    Log::debug("Found Prodi Course: " . $course->name . " (ID: " . $course->id . ")");
                }

                Log::debug("Searching Room with name: " . $row['ruangan']);
                $room = Room::where('name', $row['ruangan'])->first();
                if (!$room) {
                    $errorMsg = "Baris " . $rowNumber . ": Ruangan '{$row['ruangan']}' tidak ditemukan.";
                    $this->errors[] = $errorMsg;
                    Log::warning($errorMsg);
                    continue;
                }
                Log::debug("Found Room: " . $room->name . " (ID: " . $room->id . ")");

                Log::debug("Searching Kelas with name: " . $row['kelas']);
                $kelas = Kelas::where('name', $row['kelas'])->first();
                if (!$kelas) {
                    $errorMsg = "Baris " . $rowNumber . ": Kelas '{$row['kelas']}' tidak ditemukan.";
                    $this->errors[] = $errorMsg;
                    Log::warning($errorMsg);
                    continue;
                }
                Log::debug("Found Kelas: " . $kelas->name . " (ID: " . $kelas->id . ")");

                // Ambil dosen-dosen dan pivot data mereka
                $lecturersData = [];
                $lecturer1 = null;
                $dosen1Name = $row['dosen_1'];

                if (!empty($dosen1Name)) {
                    Log::debug("Searching Lecturer 1 with name: " . $dosen1Name);
                    $lecturer1 = Lecturer::where('nama_dosen', $dosen1Name)->first();
                    if (!$lecturer1) {
                        $errorMsg = "Baris " . $rowNumber . ": Dosen 1 '{$dosen1Name}' tidak ditemukan.";
                        $this->errors[] = $errorMsg;
                        Log::warning($errorMsg);
                        continue;
                    }
                    $lecturersData[$lecturer1->id] = [
                        'start_pertemuan' => 1,
                        'end_pertemuan' => 8
                    ];
                    Log::debug("Found Lecturer 1: " . $lecturer1->nama_dosen . " (ID: " . $lecturer1->id . ")");
                }

                $dosen2Name = $row['dosen_2'];
                if (!empty($dosen2Name)) {
                    Log::debug("Searching Lecturer 2 with name: " . $dosen2Name);
                    $lecturer2 = Lecturer::where('nama_dosen', $dosen2Name)->first();
                    if (!$lecturer2) {
                        $errorMsg = "Baris " . $rowNumber . ": Dosen 2 '{$dosen2Name}' tidak ditemukan.";
                        $this->errors[] = $errorMsg;
                        Log::warning($errorMsg);
                        continue;
                    }
                    // Validasi agar dosen 2 tidak sama dengan dosen 1
                    if ($lecturer1 && $lecturer1->id === $lecturer2->id) {
                        $errorMsg = "Baris " . $rowNumber . ": Dosen 2 tidak boleh sama dengan Dosen 1.";
                        $this->errors[] = $errorMsg;
                        Log::warning($errorMsg);
                        continue;
                    }
                    $lecturersData[$lecturer2->id] = [
                        'start_pertemuan' => 9,
                        'end_pertemuan' => 16
                    ];
                    Log::debug("Found Lecturer 2: " . $lecturer2->nama_dosen . " (ID: " . $lecturer2->id . ")");
                }

                // Jika tidak ada dosen yang ditemukan, mungkin ini adalah error, atau memang jadwal tanpa dosen (jarang)
                if (empty($lecturersData)) {
                    $errorMsg = "Baris " . $rowNumber . ": Jadwal tidak memiliki dosen yang valid. Minimal satu dosen diperlukan.";
                    $this->errors[] = $errorMsg;
                    Log::warning($errorMsg);
                }

                $schedule = Schedule::create([
                    'department_id' => $this->departmentId,
                    'academic_year_id' => $this->academicYearId,
                    'kelas_id' => $kelas->id,
                    'room_id' => $room->id,
                    'hari' => $row['hari'],
                    'start_time' => Carbon::parse($row['waktu_mulai'])->format('H:i'),
                    'end_time' => Carbon::parse($row['waktu_selesai'])->format('H:i'),
                    'schedulable_id' => $schedulableId,
                    'schedulable_type' => $schedulableType,
                ]);

                if (!empty($lecturersData)) {
                    $schedule->lecturersInSchedule()->attach($lecturersData);
                }
                Log::info("Schedule imported successfully for row " . $rowNumber . " (ID: " . $schedule->id . ")");

            } catch (\Exception $e) {
                $errorMsg = "Baris " . $rowNumber . ": Gagal memproses. " . $e->getMessage();
                $this->errors[] = $errorMsg;
                Log::error($errorMsg . " Trace: " . $e->getTraceAsString());
            }
        }
    }

    public function rules(): array
    {
        return [
            'tipe_mata_kuliah' => ['required', Rule::in(['Prodi', 'MKDU'])],
            'kode_mata_kuliah' => 'required|string',
            'kelas' => 'required|string',
            'ruangan' => 'required|string',
            'hari' => ['required', Rule::in(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'])],
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'dosen_1' => 'nullable|string',
            'dosen_2' => 'nullable|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'tipe_mata_kuliah.in' => 'Tipe Mata Kuliah harus "Prodi" atau "MKDU".',
            'waktu_mulai.date_format' => 'Format Waktu Mulai harus HH:MM (contoh: 08:00).',
            'waktu_selesai.date_format' => 'Format Waktu Selesai harus HH:MM (contoh: 09:50).',
            'waktu_selesai.after' => 'Waktu Selesai harus setelah Waktu Mulai.',
            'minggu_akhir_dosen_1.gte' => 'Minggu Akhir Dosen 1 harus lebih besar atau sama dengan Minggu Awal Dosen 1.',
            'minggu_akhir_dosen_2.gte' => 'Minggu Akhir Dosen 2 harus lebih besar atau sama dengan Minggu Awal Dosen 2.',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
