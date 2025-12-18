<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForUser($query, $user)
    {
        return $query->where('status', 'published')
            ->when($user->hasRole('admin'), function ($q) {
                // Admin dapat melihat semua acara tanpa filter
                return $q;
            })
            ->when($user->hasRole(['dekan', 'kaprodi']), function ($q) use ($user) {
                $q->where('faculty_id', $user->faculty_id)
                    ->orWhereNull('faculty_id')
                    ->orWhere('visibility', 'public');
            })
            ->when($user->hasRole('mahasiswa'), function ($q) use ($user) {
                // Pastikan relasi student ada
                if ($user->student) {
                    $q->where('department_id', $user->student->department_id)
                        ->orWhereNull('department_id')
                        ->orWhere('visibility', 'public');
                }
            })
            ->when($user->hasRole('dosen'), function ($q) use ($user) {
                // Pastikan relasi lecturer ada
                if ($user->lecturer) {
                    $q->where('faculty_id', $user->lecturer->faculty_id)
                        ->orWhereNull('faculty_id')
                        ->orWhere('visibility', 'public');
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