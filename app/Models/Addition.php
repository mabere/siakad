<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Addition extends Model
{
    use HasFactory;

    protected $table = 'penunjangs';
    protected $guarded = ['id'];

    // protected $fillable = [
    //     'lecturer_id',
    //     'title',
    //     'organizer',
    //     'level',
    //     'peran',
    //     'date',
    //     'proof',
    //     'proof_url',
    //     'status',
    //     'rejection_reason',
    //     'validated_at',
    //     'validated_by'
    // ];

    protected $casts = [
        'date' => 'date',
        'validated_at' => 'datetime'
    ];

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}