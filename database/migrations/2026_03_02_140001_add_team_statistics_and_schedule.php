<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Takım istatistikleri
        Schema::create('team_season_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('amateur_teams')->cascadeOnDelete();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();

            // Maç Bilgileri
            $table->unsignedSmallInteger('matches_played')->default(0);
            $table->unsignedSmallInteger('matches_won')->default(0);
            $table->unsignedSmallInteger('matches_drawn')->default(0);
            $table->unsignedSmallInteger('matches_lost')->default(0);

            // Skor Bilgileri
            $table->unsignedSmallInteger('goals_for')->default(0);
            $table->unsignedSmallInteger('goals_against')->default(0);
            $table->smallInteger('goal_difference')->default(0);

            // Puan
            $table->unsignedSmallInteger('points')->default(0);

            // İnsan Kaynağı
            $table->unsignedSmallInteger('total_players')->default(0);
            $table->unsignedSmallInteger('injured_players')->default(0);

            // Form
            $table->string('recent_form', 15)->nullable(); // "WDLWW"
            $table->date('last_match_date')->nullable();

            $table->timestamps();

            $table->unique(['team_id', 'season_id']);
            $table->index(['team_id', 'season_id']);
        });

        // Takım maç takvimi
        Schema::create('team_match_schedule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('amateur_teams')->cascadeOnDelete();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();

            // Hafta Bilgisi
            $table->unsignedSmallInteger('week')->default(1);
            $table->date('match_week_start')->nullable();
            $table->date('match_week_end')->nullable();

            // Maç Sayısı
            $table->unsignedSmallInteger('matches_scheduled')->default(0);
            $table->unsignedSmallInteger('matches_completed')->default(0);
            $table->unsignedSmallInteger('matches_pending')->default(0);

            // Formlar
            $table->enum('team_status', ['on_schedule', 'ahead', 'behind', 'postponed'])->default('on_schedule');

            $table->timestamps();

            $table->unique(['team_id', 'season_id', 'week']);
        });

        // Takım Oyuncu Sayısı Durum
        Schema::create('team_player_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('amateur_teams')->cascadeOnDelete();

            // Durum
            $table->unsignedSmallInteger('total_squad_size')->default(0);
            $table->unsignedSmallInteger('available_players')->default(0);
            $table->unsignedSmallInteger('injured_players')->default(0);
            $table->unsignedSmallInteger('suspended_players')->default(0);

            // Pozisyon Bazlı
            $table->unsignedSmallInteger('goalkeeper_count')->default(0);
            $table->unsignedSmallInteger('defender_count')->default(0);
            $table->unsignedSmallInteger('midfielder_count')->default(0);
            $table->unsignedSmallInteger('forward_count')->default(0);

            // Son Güncelleme
            $table->date('last_updated')->nullable();

            $table->timestamps();

            $table->unique('team_id');
        });

        // Canlı maç bilgileri (realtime updates)
        Schema::create('live_match_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->nullable()->constrained('amateur_match_records')->nullOnDelete();

            // Temel Bilgi
            $table->dateTime('update_time');
            $table->enum('status', ['scheduled', 'live', 'half_time', 'finished', 'suspended'])->default('scheduled');

            // Skor
            $table->unsignedTinyInteger('home_score')->default(0);
            $table->unsignedTinyInteger('away_score')->default(0);
            $table->unsignedTinyInteger('current_minute')->default(0);

            // Olaylar
            $table->json('events')->nullable(); // [{minute: 25, type: goal, team: home, player: Ahmet}]

            // Detaylar
            $table->text('match_commentary')->nullable();
            $table->json('possession')->nullable(); // {home: 55, away: 45}

            $table->timestamps();

            $table->index(['match_id', 'update_time']);
        });

        // Takım Performans Trendi
        Schema::create('team_performance_trends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('amateur_teams')->cascadeOnDelete();
            $table->date('calculation_date');

            // İstatistikler (Son 5 maç)
            $table->decimal('average_goals_per_match', 3, 2)->default(0);
            $table->decimal('average_goals_against', 3, 2)->default(0);
            $table->decimal('win_percentage', 5, 2)->default(0); // 0-100
            $table->decimal('clean_sheets_percentage', 5, 2)->default(0);

            // Trend
            $table->enum('trend', ['improving', 'stable', 'declining'])->default('stable');
            $table->text('performance_notes')->nullable();

            $table->timestamps();

            $table->index(['team_id', 'calculation_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_performance_trends');
        Schema::dropIfExists('live_match_updates');
        Schema::dropIfExists('team_player_availability');
        Schema::dropIfExists('team_match_schedule');
        Schema::dropIfExists('team_season_statistics');
    }
};
