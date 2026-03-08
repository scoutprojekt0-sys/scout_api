<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:video,image'],
            'file' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    $type = $this->input('type');
                    if ($type === 'video') {
                        $allowedMimes = ['mp4', 'mov', 'avi', 'wmv'];
                        $maxSize = 102400; // 100MB
                    } else {
                        $allowedMimes = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
                        $maxSize = 5120; // 5MB
                    }

                    if (!in_array($value->getClientOriginalExtension(), $allowedMimes)) {
                        $fail('Geçersiz dosya formatı.');
                    }

                    if ($value->getSize() > $maxSize * 1024) {
                        $fail('Dosya boyutu çok büyük.');
                    }
                },
            ],
            'title' => ['nullable', 'string', 'max:160'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Medya tipi zorunludur.',
            'type.in' => 'Medya tipi video veya image olmalıdır.',
            'file.required' => 'Dosya zorunludur.',
        ];
    }
}
