<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlayerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'player';
    }

    public function rules(): array
    {
        return [
            'birth_year' => ['nullable', 'integer', 'min:1950', 'max:' . date('Y')],
            'position' => ['nullable', 'string', 'max:40'],
            'dominant_foot' => ['nullable', 'in:Sağ,Sol,İkisi'],
            'height_cm' => ['nullable', 'integer', 'min:100', 'max:250'],
            'weight_kg' => ['nullable', 'integer', 'min:30', 'max:200'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'current_team' => ['nullable', 'string', 'max:120'],
        ];
    }
}
