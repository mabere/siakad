<?php

namespace App\Models;

use App\Enums\ThesisExamTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ThesisExamCriterium extends Model
{
    use HasFactory;

    protected $fillable = ['exam_type', 'name', 'weight'];

    protected $casts = [
        'exam_type' => ThesisExamTypeEnum::class,
        'weight' => 'double',
    ];

    // Relasi ke ThesisExamScore
    public function scores()
    {
        return $this->hasMany(ThesisExamScore::class, 'criteria_id');
    }
}