<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Questionnaire extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function scopeEdom($query)
    {
        return $query->where('type', 'EDOM');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'period_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function ta(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

}