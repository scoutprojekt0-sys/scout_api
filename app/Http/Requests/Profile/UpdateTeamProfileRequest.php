<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'team';
    }

    public function rules(): array
    {
        return [
            'team_name' => ['required', 'string', 'max:140'],
            'league_level' => ['nullable', 'string', 'max:60'],
            'city' => ['nullable', 'string', 'max:80'],
            'founded_year' => ['nullable', 'integer', 'min:1800', 'max:' . date('Y')],
            'needs_text' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
