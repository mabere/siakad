<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class Kegiatan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForUser($query, $user)
    {
        return $query->where('status', 'published')
            ->when($user->hasRole('admin'), function ($q) {
                return $q;
            })
            ->when($user->hasRole(['dekan', 'kaprodi']), function ($q) use ($user) {
                $facultyId = $user->lecturer?->department?->faculty_id;
                $departmentId = $user->lecturer?->department?->id;
                $userRole = $user->roles->first()?->name;
                $q->where(function ($subQuery) use ($facultyId, $departmentId, $userRole) {
                    $subQuery->where('visibility', 'public');
                    if ($facultyId) {
                        $subQuery->orWhere(function ($facultyScopedQuery) use ($facultyId) {
                            $facultyScopedQuery->where('visibility', 'faculty')
                                ->where('faculty_id', $facultyId);
                        });
                    }
                    if ($userRole === 'kaprodi' && $departmentId) {
                        $subQuery->orWhere(function ($departmentScopedQuery) use ($departmentId) {
                            $departmentScopedQuery->where('visibility', 'department')
                                ->where('department_id', $departmentId);
                        });
                    } elseif ($userRole === 'dekan' && $facultyId) {
                        $subQuery->orWhere(function ($departmentScopedQuery) use ($facultyId) {
                            $departmentScopedQuery->where('visibility', 'department')
                                ->whereHas('department', function ($depQuery) use ($facultyId) {
                                    $depQuery->where('faculty_id', $facultyId);
                                });
                        });
                    }
                })->where(function ($audienceQuery) use ($user) {
                    $audienceQuery->where('target_audience', 'semua')
                        ->orWhere('target_audience', 'dosen');
                });
            })->when($user->hasRole('mahasiswa'), function ($q) use ($user) {
                if ($user->student) {
                    $q->where(function ($subQuery) use ($user) {
                        $subQuery->where('visibility', 'public');
                        if ($user->student->department && $user->student->department->faculty_id) {
                            $subQuery->orWhere(function ($facultyScopedQuery) use ($user) {
                                $facultyScopedQuery->where('visibility', 'faculty')
                                    ->where('faculty_id', $user->student->department->faculty_id);
                            });
                        }
                        if ($user->student->department_id) {
                            $subQuery->orWhere(function ($departmentScopedQuery) use ($user) {
                                $departmentScopedQuery->where('visibility', 'department')
                                    ->where('department_id', $user->student->department_id);
                            });
                        }
                    })
                        ->where(function ($audienceQuery) {
                            $audienceQuery->where('target_audience', 'semua')
                                ->orWhere('target_audience', 'mahasiswa');
                        });
                } else {
                    $q->where('visibility', 'public')
                        ->where(function ($audienceQuery) {
                            $audienceQuery->where('target_audience', 'semua')
                                ->orWhere('target_audience', 'mahasiswa');
                        });
                }
            })
            ->when($user->hasRole('dosen'), function ($q) use ($user) {
                if ($user->lecturer) {
                    $q->where(function ($subQuery) use ($user) {
                        $subQuery->where('visibility', 'public');
                        if ($user->lecturer->faculty_id) {
                            $subQuery->orWhere(function ($facultyScopedQuery) use ($user) {
                                $facultyScopedQuery->where('visibility', 'faculty')
                                    ->where('faculty_id', $user->lecturer->faculty_id);
                            });
                        }
                        if ($user->lecturer->department_id) {
                            $subQuery->orWhere(function ($departmentScopedQuery) use ($user) {
                                $departmentScopedQuery->where('visibility', 'department')
                                    ->where('department_id', $user->lecturer->department_id);
                            });
                        }
                    })
                        ->where(function ($audienceQuery) {
                            $audienceQuery->where('target_audience', 'semua')
                                ->orWhere('target_audience', 'dosen');
                        });
                } else {

                    $q->where('visibility', 'public')
                        ->where(function ($audienceQuery) {
                            $audienceQuery->where('target_audience', 'semua')
                                ->orWhere('target_audience', 'dosen');
                        });
                }
            });
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($event) {
            Log::info('Creating event:', $event->toArray());
        });
        static::created(function ($event) {
            Log::info('Event created:', ['id' => $event->id, 'data' => $event->toArray()]);
        });
        static::saving(function ($event) {
            Log::info('Saving event:', $event->toArray());
        });
        static::saved(function ($event) {
            Log::info('Event saved:', ['id' => $event->id, 'data' => $event->toArray()]);
        });
    }


}