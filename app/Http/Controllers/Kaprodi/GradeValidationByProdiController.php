<?php

namespace App\Http\Controllers\Kaprodi;

use App\Models\Grade;
use App\Models\Course;
use App\Models\Schedule;
use App\Models\MkduCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;

class GradeValidationByProdiController extends Controller
{
    // Bagian Kaprodi/Staff
    public function showValidation(Request $request)
    {
        try {
            $user = auth()->user()->load(['employee', 'lecturer']);
            $isKaprodi = $user->hasRole('kaprodi');
            $isStaff = $user->hasRole('staff');

            if (!$isKaprodi && !$isStaff) {
                return redirect()->route('dashboard')
                    ->with('error', 'Anda tidak memiliki hak akses ke halaman ini.');
            }

            $schedules = Schedule::query();
            $schedules->with([
                'lecturersInSchedule',
                'grades',
                'kelas',
                'schedulable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Course::class => ['department'],
                        MkduCourse::class => [],
                    ]);
                },
            ]);

            $schedules->whereHas('grades', function ($query) {
                $query->whereIn('validation_status', ['pending', 'dosen_validated', 'kaprodi_approved', 'locked']);
            });

            if ($isStaff) {
                if (!$user->employee || !$user->employee->department_id) {
                    return redirect()->route('dashboard')
                        ->with('error', 'Data department employee tidak ditemukan atau tidak valid. Hubungi administrator.');
                }
                $staffDepartmentId = $user->employee->department_id;

                $schedules->where(function ($query) use ($staffDepartmentId) {
                    $query->whereHasMorph(
                        'schedulable',
                        [Course::class],
                        function ($morphQuery) use ($staffDepartmentId) {
                            $morphQuery->where('department_id', $staffDepartmentId);
                        }
                    )->orWhere('schedulable_type', MkduCourse::class);
                });

            } elseif ($isKaprodi) {
                if (!$user->lecturer || !$user->lecturer->department_id) {
                    return redirect()->route('dashboard')
                        ->with('error', 'Data department lecturer tidak ditemukan atau tidak valid. Hubungi administrator.');
                }
                $kaprodiDepartmentId = $user->lecturer->department_id;

                // --- Logika filter untuk Kaprodi di sini ---
                $schedules->where(function ($query) use ($kaprodiDepartmentId) {
                    $query->whereHasMorph(
                        'schedulable',
                        [Course::class],
                        function ($morphQuery) use ($kaprodiDepartmentId) {
                            $morphQuery->where('department_id', $kaprodiDepartmentId);
                        }
                    )->orWhere('schedulable_type', MkduCourse::class);
                });
            }

            $schedules = $schedules->get();
            return view('staff.nilai.validasi', compact('schedules'));

        } catch (\Exception $e) {
            Log::error('Show Validation Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat halaman validasi: ' . $e->getMessage());
        }
    }

    public function approveByProdi(Request $request, $id)
    {
        try {
            $user = auth()->user()->load('lecturer');

            $jadwal = Schedule::with([
                'grades',
                'schedulable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Course::class => ['department'],
                        MkduCourse::class => [],
                    ]);
                },
            ])->findOrFail($id);
            $sampleGrade = $jadwal->grades->first();
            if (!$sampleGrade) {
                return back()->with('error', 'Tidak ada nilai untuk disetujui.');
            }
            $this->authorize('approveByProdi', $sampleGrade);
            DB::beginTransaction();
            foreach ($jadwal->grades as $grade) {
                $grade->validation_status = 'kaprodi_approved';
                $grade->save();
            }
            DB::commit();
            return redirect()->route('kaprodi.nilai.validasi')
                ->with('success', 'Validasi telah disetujui oleh prodi.');
        } catch (AuthorizationException $e) {
            Log::warning('Authorization Error during Prodi Approval: ' . $e->getMessage(), ['user_id' => auth()->id(), 'schedule_id' => $id]);
            return back()->with('error', 'Anda tidak memiliki hak akses atau nilai belum siap untuk disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approve by Prodi Error: ' . $e->getMessage(), ['schedule_id' => $id, 'exception' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat persetujuan prodi.');
        }
    }

    // Bagian KTU/Staff
    public function lockGrades(Request $request, $id)
    {
        try {
            $user = auth()->user()->load('employee');
            $jadwal = Schedule::with([
                'grades',
                'schedulable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Course::class => ['department'],
                        MkduCourse::class => [],
                    ]);
                },
            ])->findOrFail($id);
            $sampleGrade = $jadwal->grades->first();
            if (!$sampleGrade) {
                return back()->with('error', 'Tidak ada nilai untuk dikunci.');
            }
            $this->authorize('lockGrades', $sampleGrade);
            DB::beginTransaction();
            foreach ($jadwal->grades as $grade) {
                $grade->validation_status = 'locked';
                $grade->save();
            }
            DB::commit();
            return redirect()->route('staff.nilai.validasi')
                ->with('success', 'Nilai telah dikunci dan tidak dapat diubah.');
        } catch (AuthorizationException $e) {
            Log::warning('Authorization Error during Grade Lock: ' . $e->getMessage(), ['user_id' => auth()->id(), 'schedule_id' => $id]);
            return back()->with('error', 'Anda tidak memiliki hak akses atau nilai belum siap untuk dikunci.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lock Grades Error: ' . $e->getMessage(), ['schedule_id' => $id, 'exception' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat penguncian nilai: ' . $e->getMessage());
        }
    }

}
