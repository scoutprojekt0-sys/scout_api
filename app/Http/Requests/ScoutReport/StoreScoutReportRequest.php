<?php

namespace App\Http\Requests\ScoutReport;

use Illuminate\Foundation\Http\FormRequest;

class StoreScoutReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()->role, ['scout', 'coach', 'manager']);
    }

    public function rules(): array
    {
        return [
            'player_user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:160'],
            'technical_assessment' => ['nullable', 'string', 'max:2000'],
            'physical_assessment' => ['nullable', 'string', 'max:2000'],
            'mental_assessment' => ['nullable', 'string', 'max:2000'],
            'overall_rating' => ['nullable', 'integer', 'min:1', 'max:100'],
            'recommendation' => ['required', 'in:highly_recommended,recommended,neutral,not_recommended'],
            'watched_date' => ['nullable', 'date', 'before_or_equal:today'],
            'watched_location' => ['nullable', 'string', 'max:120'],
            'is_private' => ['boolean'],
        ];
    }
}
