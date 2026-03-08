<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerAttribute extends Model
{
    protected $fillable = [
        'player_user_id', 'pace', 'shooting', 'passing', 'dribbling',
        'defending', 'physicality', 'finishing', 'heading_accuracy',
        'free_kick_accuracy', 'shot_power', 'long_shots', 'vision',
        'crossing', 'ball_control', 'agility', 'stamina', 'strength',
        'aggression', 'positioning', 'composure', 'work_rate_attack',
        'work_rate_defense', 'strong_foot', 'strengths', 'weaknesses',
    ];

    protected function casts(): array
    {
        return [
            'strengths' => 'array',
            'weaknesses' => 'array',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_user_id');
    }

    // Overall rating hesaplama
    public function calculateOverall(): int
    {
        $attributes = [
            $this->pace, $this->shooting, $this->passing,
            $this->dribbling, $this->defending, $this->physicality
        ];

        $validAttributes = array_filter($attributes, fn($val) => $val !== null);

        return count($validAttributes) > 0
            ? (int) round(array_sum($validAttributes) / count($validAttributes))
            : 0;
    }
}
