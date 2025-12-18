<?php

namespace App\Services;

use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScheduleService
{
    public function checkConflict(array $scheduleData, array $lecturersData, ?int $excludeScheduleId = null): array
    {
        $conflicts = [];

        $hari = $scheduleData['hari'];
        $startTime = $scheduleData['start_time'];
        $endTime = $scheduleData['end_time'];
        $roomId = $scheduleData['room_id'];
        $kelasId = $scheduleData['kelas_id'];
        $academicYearId = $scheduleData['academic_year_id'];
        $lecturerIds = array_keys($lecturersData);

        $baseQuery = Schedule::where('hari', $hari)
            ->where('academic_year_id', $academicYearId)
            ->where(function ($query) use ($startTime, $endTime) {
                // Check for overlapping times
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                        ->where('end_time', '>', $startTime);
                });
            });

        if ($excludeScheduleId) {
            $baseQuery->where('id', '!=', $excludeScheduleId);
        }

        // 1. Check Room Conflict
        if ((clone $baseQuery)->where('room_id', $roomId)->exists()) {
            $conflicts[] = 'Ruangan ini sudah digunakan pada jam dan hari tersebut.';
        }

        // 2. Check Class Conflict
        if ((clone $baseQuery)->where('kelas_id', $kelasId)->exists()) {
            $conflicts[] = 'Kelas ini sudah memiliki jadwal lain pada jam dan hari tersebut.';
        }

        // 3. Check Lecturer Conflict
        if (!empty($lecturerIds)) {
            $busyLecturers = (clone $baseQuery)
                ->whereHas('lecturers', function ($query) use ($lecturerIds) {
                    $query->whereIn('lecturers.id', $lecturerIds);
                })->exists();
            if ($busyLecturers) {
                $conflicts[] = 'Salah satu dosen yang dipilih sudah memiliki jadwal lain pada jam dan hari tersebut.';
            }
        }
        return $conflicts;
    }

    public function createSchedule(array $scheduleData, array $lecturersData): Schedule
    {
        return DB::transaction(function () use ($scheduleData, $lecturersData) {
            $schedule = Schedule::create($scheduleData);
            if (!empty($lecturersData)) {
                $schedule->lecturers()->attach($lecturersData);
            }
            Log::info('Schedule created successfully', ['schedule_id' => $schedule->id, 'data' => $scheduleData]);
            return $schedule;
        });
    }

    public function updateSchedule(Schedule $schedule, array $scheduleData, array $lecturersData): Schedule
    {
        return DB::transaction(function () use ($schedule, $scheduleData, $lecturersData) {
            $schedule->update($scheduleData);
            $schedule->lecturers()->sync($lecturersData);
            Log::info('Schedule updated successfully', ['schedule_id' => $schedule->id, 'data' => $scheduleData]);
            return $schedule;
        });
    }

}