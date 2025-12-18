<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThesisDocument extends Model
{
    protected $guarded = ['id'];

    public function thesisExam()
    {
        return $this->belongsTo(ThesisExam::class);
    }

}