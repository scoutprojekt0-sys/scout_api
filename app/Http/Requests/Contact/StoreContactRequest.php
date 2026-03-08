<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to_user_id' => ['required', 'exists:users,id', 'different:from_user_id'],
            'subject' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'to_user_id.required' => 'Alıcı kullanıcı zorunludur.',
            'to_user_id.exists' => 'Alıcı kullanıcı bulunamadı.',
            'message.required' => 'Mesaj içeriği zorunludur.',
        ];
    }
}
