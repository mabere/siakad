<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupervisionMeeting extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'thesis_id',
        'supervisor_id',
        'meeting_date',
        'topic',
        'description',
        'attachment_path',
        'status', // pending, approved, rejected
        'notes',
        'response_date'
    ];

    protected $casts = [
        'meeting_date' => 'datetime',
        'response_date' => 'datetime'
    ];

    public function supervision(): BelongsTo
    {
        return $this->belongsTo(ThesisSupervision::class, 'thesis_supervision_id');
    }

    public function thesis()
    {
        return $this->belongsTo(Thesis::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(Lecturer::class, 'supervisor_id');
    }

    public function scopeHasPendingMeetings($query, $thesis_id, $supervisor_id)
    {
        return $query->where('thesis_id', $thesis_id)
            ->where('supervisor_id', $supervisor_id)
            ->where('status', 'pending')
            ->exists();
    }

}