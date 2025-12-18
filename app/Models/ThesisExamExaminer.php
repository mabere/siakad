<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThesisExamExaminer extends Model
{
    protected $guarded = ['id'];

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

}
