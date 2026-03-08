<?php

namespace App\Http\Requests\Opportunity;

use Illuminate\Foundation\Http\FormRequest;

class StoreOpportunityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'team';
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'position' => ['nullable', 'string', 'max:40'],
            'age_min' => ['nullable', 'integer', 'min:10', 'max:99'],
            'age_max' => ['nullable', 'integer', 'min:10', 'max:99', 'gte:age_min'],
            'city' => ['nullable', 'string', 'max:80'],
            'details' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Başlık zorunludur.',
            'age_max.gte' => 'Maksimum yaş, minimum yaştan büyük veya eşit olmalıdır.',
        ];
    }
}
