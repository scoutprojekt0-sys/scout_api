<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    protected $table = 'faq';

    protected $fillable = [
        'question', 'answer', 'user_type', 'topic', 'view_count',
        'helpful_count', 'is_active', 'order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function incrementViews(): void
    {
        $this->increment('view_count');
    }

    public function markHelpful(): void
    {
        $this->increment('helpful_count');
    }
}
