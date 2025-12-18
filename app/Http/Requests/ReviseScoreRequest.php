<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviseScoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Route-nya adalah /thesis/{thesis}/score/{examiner}/revise
        // Jadi kita bisa akses 'thesis' dari parameter route
        return $this->user()->can('reviseScore', $this->route('thesis'));
    }

    public function rules(): array
    {
        return [
            'score' => 'required|numeric|min:0|max:100',
            'reason' => 'required|string|max:500',
        ];
    }
}