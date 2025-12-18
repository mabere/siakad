<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EdomSetting extends Model
{
    protected $fillable = [
        'min_respondents',
        'submission_deadline',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}