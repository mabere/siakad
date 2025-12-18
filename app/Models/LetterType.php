<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LetterType extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'required_fields' => 'array',
        'needs_approval' => 'boolean',
        'approval_flow' => 'array',
    ];

    public function requests(): HasMany
    {
        return $this->hasMany(LetterRequest::class, 'letter_type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function letterTypeAssignments()
    {
        return $this->hasMany(LetterTypeAssignment::class, 'letter_type_id');
    }

    public function suratajuan()
    {
        return $this->belongsTo(LetterTypeAssignment::class, 'letter_type_id');
    }
}