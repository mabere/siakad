<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Student;
use App\Models\Lecturer;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    const ROLE_ADMIN = 'admin';
    const ROLE_UJM = 'ujm';
    const ROLE_DEKAN = 'dekan';
    const ROLE_KAPRODI = 'kaprodi';
    const ROLE_DOSEN = 'dosen';
    const ROLE_MAHASISWA = 'mahasiswa';
    const ROLE_REKTOR = 'rektor';
    const ROLE_WAREK = 'warek';
    const ROLE_DIREKTUR = 'direktur';

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'metadata' => 'array',
            'active' => 'boolean'
        ];
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function faculty()
    {
        return $this->hasOne(Faculty::class);
    }

    public function department()
    {
        return $this->hasOneThrough(
            Department::class,
            Student::class,
            'user_id', // Foreign key di students
            'id',      // Primary key di departments
            'id',      // Primary key di users
            'department_id' // Foreign key di students yang menunjuk ke departments
        );
    }

    public function staff()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id', 'id');
    }

    public function lecturer(): HasOne
    {
        return $this->hasOne(Lecturer::class);
    }

    public function dekan(): HasOne
    {
        return $this->hasOne(Lecturer::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class, 'student_id');
    }

    public function hasRoleInUnit($role, $unitId)
    {
        return $this->assignments()
            ->where('role', $role)
            ->where('unit_id', $unitId)
            ->where('active', true)
            ->exists();
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN; // Asumsi role 1 adalah Admin
    }

    public function alumni()
    {
        return $this->hasOneThrough(Alumni::class, Student::class, 'user_id', 'student_id', 'id', 'id');
    }

    public function isAlumni()
    {
        return $this->hasRole('alumni') || ($this->hasRole('mahasiswa') && $this->student && $this->student->alumni);
    }

    // Relasi dengan ajuan surat sebagai penyetuju
    public function approvedLetters()
    {
        return $this->hasMany(LetterRequest::class, 'approved_by');
    }

    public function letterRequests()
    {
        return $this->hasMany(LetterRequest::class, 'user_id');
    }

    public function isLeader()
    {
        return in_array($this->role, [
            self::ROLE_DEKAN,
            self::ROLE_KAPRODI
        ]);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function hasRole($role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    // Get all active roles
    public function getActiveRoles()
    {
        return $this->assignments()
            ->where('active', true)
            ->get()
            ->pluck('role')
            ->unique();
    }

    public function hasAnyRole($roles)
    {
        return $this->roles()->whereIn('name', (array) $roles)->exists();
    }

    public function getRoleNames()
    {
        return $this->roles->pluck('name'); // mengambil nama semua role
    }

    public function activeRole(): ?string
    {
        return $this->active_role ?? $this->getRoleNames()->first();
    }


}
