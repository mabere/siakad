<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Faculty extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function kegiatans()
    {
        return $this->hasMany(Kegiatan::class);
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, Department::class);
    }

    public function dekanUser()
    {
        return $this->belongsTo(User::class, 'dekan_user_id');
    }

}