<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageTranslation extends Model
{
    protected $fillable = [
        'language_code', 'key', 'value', 'category',
    ];

    protected function casts(): array
    {
        return [
        ];
    }
}
