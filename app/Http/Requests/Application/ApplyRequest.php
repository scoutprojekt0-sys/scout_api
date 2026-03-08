<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;

class ApplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'player';
    }

    public function rules(): array
    {
        return [
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.max' => 'Mesaj maksimum 2000 karakter olabilir.',
        ];
    }
}
