<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterTypeAssignment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'required_fields' => 'array',
        'is_active' => 'boolean',
        'faculty_id' => 'integer',
        'department_id' => 'integer',
        'approval_flow' => 'array',
    ];
}