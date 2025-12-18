<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LetterRequest extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'form_data' => 'array',
        'approval_flow' => 'array',
        'approval_history' => 'array',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function letterType(): BelongsTo
    {
        return $this->belongsTo(LetterType::class, 'letter_type_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'submitted', 'rejected']);
    }

    public function getCurrentStepAttribute()
    {
        if (!$this->approval_flow)
            return null;
        foreach ($this->approval_flow as $step => $status) {
            if ($status === 'pending')
                return $step;
        }
        return null;
    }

    public function approvalHistoryUsers()
    {
        $userIds = collect($this->approval_history)->pluck('user_id')->unique()->filter();
        return $this->belongsToMany(User::class, 'letter_requests', 'id', 'user_id')
            ->whereIn('users.id', $userIds->isNotEmpty() ? $userIds : [0]); // Prevent empty whereIn
    }

    public function generateReferenceNumber()
    {
        $prefix = $this->letterType->code;
        $year = date('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf("%s/%d/%03d", $prefix, $year, $count);
    }

}