<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'user_id', 'department_id', 'position', 'nip', 'alamat', 'tgl', 'tpl', 'telp'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id'); // Asumsi ada kolom faculty_id
    }

    public function isFacultyLevel(): bool
    {
        return $this->level === 'faculty';
    }

    public function isDepartmentLevel(): bool
    {
        return $this->level === 'department';
    }
}
