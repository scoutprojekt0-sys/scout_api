<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // AMATÖR FUTBOLCU PİYASA DEĞERİ
        Schema::create('amateur_player_market_value', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('users')->cascadeOnDelete();

            // TEMEL DEĞER (Base Value)
            $table->unsignedInteger('base_value')->default(5000); // Başlangıç: 5.000

            // PİYASA PUANLARI (Market Points)
            $table->unsignedInteger('profile_views_points')->default(0);    // Profil Görünüm Puanları
            $table->unsignedInteger('engagement_points')->default(0);       // Beğeni/Yorum Puanları
            $table->unsignedInteger('performance_points')->default(0);      // Performans Puanları
            $table->unsignedInteger('trending_points')->default(0);         // Trend Puanları (Haftalık)
            $table->unsignedInteger('scout_interest_points')->default(0);   // Scout İlgi Puanları

            // HESAPLANAN PİYASA DEĞERİ
            $table->unsignedInteger('calculated_market_value')->default(5000);

            // TREND
            $table->unsignedSmallInteger('price_trend')->default(0); // +/- %
            $table->string('trend_status', 50)->default('stable'); // up, down, stable
            $table->dateTime('last_updated')->nullable();

            // SIRA
            $table->unsignedSmallInteger('market_rank')->nullable();

            $table->timestamps();

            $table->unique('player_id');
            $table->index('calculated_market_value');
        });

        // PİYASA PUANINI ETKILEYECEK AKSIYON LOGları
        Schema::create('market_point_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('users')->cascadeOnDelete();

            // AKSIYON TÜRÜ
            $table->enum('action_type', [
                'profile_view',        // Profil Görüntüleme
                'like',                // Beğeni
                'comment',             // Yorum
                'save',                // Kaydetme
                'match_goal',          // Maç Golü
                'match_assist',        // Maç Asisti
                'mvp',                 // Maçın MVP'si
                'scout_viewed',        // Scout Bakışı
                'scout_interest',      // Scout İlgi Gösterimi
                'share',               // Paylaşıma
            ])->default('profile_view');

            // PUAN DEĞERİ
            $table->unsignedSmallInteger('points_gained')->default(0);

            // DETAY
            $table->text('description')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();

            // SIRA
            $table->unsignedInteger('running_total')->default(0);

            $table->timestamps();

            $table->index(['player_id', 'created_at']);
        });

        // HAFTALIK TREND (En Popüler Oyuncular)
        Schema::create('weekly_trending_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('users')->cascadeOnDelete();

            $table->unsignedInteger('weekly_points')->default(0);
            $table->unsignedInteger('weekly_views')->default(0);
            $table->unsignedInteger('weekly_rank')->default(0);
            $table->date('week_start');
            $table->date('week_end');

            $table->timestamps();

            $table->index(['week_start', 'weekly_points']);
        });

        // AMATÖR FUTBOL PİYASA İSTATİSTİKLERİ
        Schema::create('amateur_market_statistics', function (Blueprint $table) {
            $table->id();

            // GENEL İSTATİSTİKLER
            $table->unsignedInteger('total_players')->default(0);
            $table->unsignedInteger('active_players')->default(0); // Son 30 gün
            $table->decimal('average_market_value', 12, 2)->default(0);
            $table->unsignedInteger('highest_value')->nullable();
            $table->unsignedInteger('lowest_value')->nullable();

            // TRENDLERİ
            $table->unsignedInteger('trending_up_count')->default(0);
            $table->unsignedInteger('trending_down_count')->default(0);
            $table->unsignedInteger('stable_count')->default(0);

            // İŞLEMLER
            $table->unsignedInteger('daily_profile_views')->default(0);
            $table->unsignedInteger('daily_likes')->default(0);
            $table->unsignedInteger('daily_comments')->default(0);

            $table->date('statistics_date');
            $table->timestamps();

            $table->unique('statistics_date');
        });

        // AMATÖR TRANSFER TEKLIFI
        Schema::create('amateur_transfer_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('from_team_id')->constrained('users')->cascadeOnDelete();

            // TEKLIK DETAYLARI
            $table->text('offer_message')->nullable();
            $table->unsignedInteger('proposed_value')->nullable();

            // DURUM
            $table->enum('status', [
                'pending',
                'accepted',
                'rejected',
                'expired'
            ])->default('pending');

            $table->dateTime('expires_at')->nullable();
            $table->dateTime('responded_at')->nullable();

            $table->timestamps();

            $table->index(['player_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amateur_transfer_offers');
        Schema::dropIfExists('amateur_market_statistics');
        Schema::dropIfExists('weekly_trending_players');
        Schema::dropIfExists('market_point_logs');
        Schema::dropIfExists('amateur_player_market_value');
    }
};
