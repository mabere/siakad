<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThesisExam extends Model
{
    protected $fillable = [
        'thesis_id',
        'exam_type',
        'chairman_id',
        'secretary_id',
        'scheduled_at',
        'location',
        'status',
        'revisi_notes',
        'final_score',
        'notes',
    ];

    protected $dates = ['scheduled_at'];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    const STATUS_DIAJUKAN = 'diajukan';
    const STATUS_TERVERIFIKASI = 'terverifikasi';
    const STATUS_REVISI = 'revisi';
    const STATUS_DITOLAK = 'rejected';
    const STATUS_PENGUJI_DITETAPKAN = 'penguji_ditetapkan';
    const STATUS_DISETUJUI_DEKAN = 'disetujui_dekan';
    const STATUS_REVISI_DEKAN = 'revisi_dekan';
    const STATUS_DIJADWALKAN = 'dijadwalkan';
    const STATUS_DILAKSANAKAN = 'pelaksanaan';
    const STATUS_SELESAI = 'selesai';

    const STATUS_LULUS = 'lulus';

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    public function documents()
    {
        return $this->hasMany(ThesisDocument::class);
    }

    public function examiners(): HasMany
    {
        return $this->hasMany(ThesisExamExaminer::class);
    }

    public function chairman()
    {
        return $this->belongsTo(Lecturer::class, 'chairman_id');
    }

    public function secretary()
    {
        return $this->belongsTo(Lecturer::class, 'secretary_id');
    }

    public function scores()
    {
        return $this->hasMany(ThesisExamScore::class);
    }

    public function criteria()
    {
        return ThesisExamCriterium::where('exam_type', $this->exam_type)->get();
    }


}