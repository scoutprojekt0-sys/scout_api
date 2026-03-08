<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmateurPlayerMarketValue extends Model
{
    protected $fillable = [
        'player_id', 'base_value', 'profile_views_points', 'engagement_points',
        'performance_points', 'trending_points', 'scout_interest_points',
        'calculated_market_value', 'price_trend', 'trend_status', 'last_updated',
        'market_rank',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_id');
    }

    public function pointLogs(): HasMany
    {
        return $this->hasMany(MarketPointLog::class, 'player_id');
    }

    /**
     * Piyasa Değerini Hesapla
     */
    public function calculateMarketValue(): void
    {
        $baseValue = $this->base_value; // 5000

        // Tüm puanları topla
        $totalPoints = $this->profile_views_points + $this->engagement_points +
                      $this->performance_points + $this->trending_points +
                      $this->scout_interest_points;

        // Her puan = 100 değer (Örnek: 100 puan = +10.000)
        $calculatedValue = $baseValue + ($totalPoints * 100);

        // Maksimum değer sınırı (1.000.000)
        $calculatedValue = min($calculatedValue, 1000000);

        $this->calculated_market_value = $calculatedValue;
        $this->last_updated = now();
        $this->save();
    }

    /**
     * Puan Ekle (TIklandığında, Beğenildiğinde, vb)
     */
    public function addPoints(string $actionType, int $points = 1, ?string $description = null, ?int $relatedId = null): void
    {
        // Puan Logu Oluştur
        MarketPointLog::create([
            'player_id' => $this->player_id,
            'action_type' => $actionType,
            'points_gained' => $points,
            'description' => $description,
            'related_id' => $relatedId,
        ]);

        // İlgili Puanı Artır
        match($actionType) {
            'profile_view' => $this->increment('profile_views_points', $points),
            'like' => $this->increment('engagement_points', $points),
            'comment' => $this->increment('engagement_points', $points * 2),
            'save' => $this->increment('engagement_points', $points),
            'match_goal' => $this->increment('performance_points', $points * 5),
            'match_assist' => $this->increment('performance_points', $points * 3),
            'mvp' => $this->increment('performance_points', $points * 10),
            'scout_viewed' => $this->increment('scout_interest_points', $points * 2),
            'scout_interest' => $this->increment('scout_interest_points', $points * 5),
            'share' => $this->increment('trending_points', $points),
            default => null,
        };

        // Piyasa Değerini Yeniden Hesapla
        $this->calculateMarketValue();
    }

    /**
     * Trend Durumunu Belirle
     */
    public function updateTrendStatus(): void
    {
        // Son haftanın puanlarını al
        $weekAgoPoints = MarketPointLog::where('player_id', $this->player_id)
            ->where('created_at', '>=', now()->subWeek())
            ->sum('points_gained');

        // Önceki haftanın puanlarını al
        $twoWeeksAgoPoints = MarketPointLog::where('player_id', $this->player_id)
            ->whereBetween('created_at', [now()->subWeeks(2), now()->subWeek()])
            ->sum('points_gained');

        if ($twoWeeksAgoPoints > 0) {
            $trendPercent = (($weekAgoPoints - $twoWeeksAgoPoints) / $twoWeeksAgoPoints) * 100;

            $this->price_trend = intval($trendPercent);

            if ($trendPercent > 5) {
                $this->trend_status = 'up';
            } elseif ($trendPercent < -5) {
                $this->trend_status = 'down';
            } else {
                $this->trend_status = 'stable';
            }

            $this->save();
        }
    }
}
