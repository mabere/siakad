<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurriculumEvaluation extends Model
{
    protected $fillable = ['curriculum_id', 'evaluated_at', 'notes', 'status'];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }
}
