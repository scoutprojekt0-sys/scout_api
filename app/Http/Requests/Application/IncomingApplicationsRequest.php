<?php

namespace App\Http\Requests\Application;

use App\Models\Application;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IncomingApplicationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewIncoming', Application::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', Rule::in(['pending', 'accepted', 'rejected'])],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
