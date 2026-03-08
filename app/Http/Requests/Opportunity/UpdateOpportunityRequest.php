<?php

namespace App\Http\Requests\Opportunity;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOpportunityRequest extends FormRequest
{
    public function authorize(): bool
    {
        $opportunity = $this->route('opportunity');
        return $this->user()->id === $opportunity->team_user_id;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:160'],
            'position' => ['nullable', 'string', 'max:40'],
            'age_min' => ['nullable', 'integer', 'min:10', 'max:99'],
            'age_max' => ['nullable', 'integer', 'min:10', 'max:99', 'gte:age_min'],
            'city' => ['nullable', 'string', 'max:80'],
            'details' => ['nullable', 'string', 'max:5000'],
            'status' => ['sometimes', 'in:open,closed'],
        ];
    }
}
