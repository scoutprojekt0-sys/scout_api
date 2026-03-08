<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Gelişmiş Oyuncu İstatistikleri (Detaylı)
        Schema::create('player_detailed_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
            $table->foreignId('league_id')->nullable()->constrained('leagues')->nullOnDelete();

            // Temel İstatistikler
            $table->unsignedSmallInteger('appearances')->default(0); // Maç sayısı
            $table->unsignedSmallInteger('starts')->default(0); // İlk 11
            $table->unsignedSmallInteger('substitutions_on')->default(0);
            $table->unsignedSmallInteger('substitutions_off')->default(0);
            $table->unsignedInteger('minutes_played')->default(0);

            // Gol ve Asist
            $table->unsignedSmallInteger('goals')->default(0);
            $table->unsignedSmallInteger('assists')->default(0);
            $table->unsignedSmallInteger('penalties_scored')->default(0);
            $table->unsignedSmallInteger('penalties_missed')->default(0);
            $table->unsignedSmallInteger('own_goals')->default(0);

            // Kartlar ve Disiplin
            $table->unsignedSmallInteger('yellow_cards')->default(0);
            $table->unsignedSmallInteger('red_cards')->default(0);
            $table->unsignedSmallInteger('second_yellow_cards')->default(0);

            // Şut İstatistikleri
            $table->unsignedSmallInteger('shots_on_target')->default(0);
            $table->unsignedSmallInteger('shots_off_target')->default(0);
            $table->decimal('shot_accuracy', 5, 2)->nullable(); // %

            // Pas İstatistikleri
            $table->unsignedSmallInteger('passes_completed')->default(0);
            $table->unsignedSmallInteger('passes_attempted')->default(0);
            $table->decimal('pass_accuracy', 5, 2)->nullable(); // %
            $table->unsignedSmallInteger('key_passes')->default(0);

            // Savunma İstatistikleri
            $table->unsignedSmallInteger('tackles')->default(0);
            $table->unsignedSmallInteger('interceptions')->default(0);
            $table->unsignedSmallInteger('clearances')->default(0);
            $table->unsignedSmallInteger('blocks')->default(0);

            // Hava Topu
            $table->unsignedSmallInteger('aerial_duels_won')->default(0);
            $table->unsignedSmallInteger('aerial_duels_lost')->default(0);

            // Dripling
            $table->unsignedSmallInteger('dribbles_completed')->default(0);
            $table->unsignedSmallInteger('dribbles_attempted')->default(0);

            // Kaleci İstatistikleri
            $table->unsignedSmallInteger('saves')->default(0);
            $table->unsignedSmallInteger('clean_sheets')->default(0);
            $table->unsignedSmallInteger('goals_conceded')->default(0);
            $table->unsignedSmallInteger('penalties_saved')->default(0);

            // Performans Değerlendirmesi
            $table->decimal('average_rating', 3, 1)->nullable(); // 0.0-10.0
            $table->unsignedSmallInteger('man_of_the_match')->default(0);

            $table->timestamps();

            $table->unique(['player_user_id', 'club_id', 'season_id', 'league_id'], 'player_club_season_league_unique');
            $table->index(['player_user_id', 'season_id']);
        });

        // Maç Detayları
        Schema::create('match_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained('leagues')->cascadeOnDelete();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
            $table->foreignId('home_club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('away_club_id')->constrained('clubs')->cascadeOnDelete();
            $table->dateTime('match_date');
            $table->string('venue', 120)->nullable();
            $table->unsignedTinyInteger('home_score')->nullable();
            $table->unsignedTinyInteger('away_score')->nullable();
            $table->unsignedTinyInteger('home_halftime_score')->nullable();
            $table->unsignedTinyInteger('away_halftime_score')->nullable();
            $table->enum('match_type', ['league', 'cup', 'playoff', 'friendly', 'international'])->default('league');
            $table->enum('status', ['scheduled', 'live', 'finished', 'postponed', 'cancelled', 'abandoned'])->default('scheduled');
            $table->unsignedSmallInteger('round')->nullable(); // Hafta
            $table->unsignedInteger('attendance')->nullable();
            $table->string('referee', 100)->nullable();
            $table->text('match_report')->nullable();
            $table->timestamps();

            $table->index(['league_id', 'season_id', 'match_date']);
            $table->index(['home_club_id', 'away_club_id']);
        });

        // Maç Kadrosu (Oyuncu Performansları)
        Schema::create('match_player_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('match_details')->cascadeOnDelete();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();

            $table->unsignedTinyInteger('shirt_number')->nullable();
            $table->boolean('is_starter')->default(false);
            $table->unsignedSmallInteger('minutes_played')->default(0);
            $table->unsignedTinyInteger('substituted_in_minute')->nullable();
            $table->unsignedTinyInteger('substituted_out_minute')->nullable();

            $table->unsignedTinyInteger('goals')->default(0);
            $table->unsignedTinyInteger('assists')->default(0);
            $table->unsignedTinyInteger('yellow_cards')->default(0);
            $table->unsignedTinyInteger('red_cards')->default(0);
            $table->boolean('own_goal')->default(false);

            $table->decimal('rating', 3, 1)->nullable();
            $table->boolean('man_of_the_match')->default(false);

            $table->timestamps();

            $table->index(['match_id', 'club_id']);
            $table->index(['player_user_id', 'match_id']);
        });

        // Takım Kadro Değeri Geçmişi
        Schema::create('club_market_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->decimal('total_market_value', 15, 2);
            $table->decimal('average_market_value', 12, 2);
            $table->date('valuation_date');
            $table->unsignedSmallInteger('squad_size')->default(0);
            $table->timestamps();

            $table->index(['club_id', 'valuation_date']);
        });

        // Transfer Söylentileri / Dedikodular
        Schema::create('transfer_rumors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('from_club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->foreignId('to_club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->string('source', 200)->nullable(); // Haber kaynağı
            $table->enum('reliability', ['very_high', 'high', 'medium', 'low', 'very_low'])->default('medium');
            $table->decimal('estimated_fee', 12, 2)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'completed', 'denied', 'expired'])->default('active');
            $table->date('rumor_date');
            $table->timestamps();

            $table->index(['player_user_id', 'status']);
        });

        // Oyuncu Karşılaştırma Logları
        Schema::create('player_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->json('player_ids'); // [1, 2, 3] - Karşılaştırılan oyuncular
            $table->foreignId('season_id')->nullable()->constrained('seasons')->nullOnDelete();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
        });

        // Lig Puan Durumu
        Schema::create('league_standings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained('leagues')->cascadeOnDelete();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->unsignedTinyInteger('position')->default(1);
            $table->unsignedSmallInteger('played')->default(0);
            $table->unsignedSmallInteger('won')->default(0);
            $table->unsignedSmallInteger('drawn')->default(0);
            $table->unsignedSmallInteger('lost')->default(0);
            $table->unsignedSmallInteger('goals_for')->default(0);
            $table->unsignedSmallInteger('goals_against')->default(0);
            $table->smallInteger('goal_difference')->default(0);
            $table->unsignedSmallInteger('points')->default(0);
            $table->string('form', 10)->nullable(); // "WWDLW"
            $table->timestamps();

            $table->unique(['league_id', 'season_id', 'club_id']);
            $table->index(['league_id', 'season_id', 'position']);
        });

        // Oyuncu Güçlü/Zayıf Yönler
        Schema::create('player_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();

            // Teknik (0-100)
            $table->unsignedTinyInteger('pace')->nullable();
            $table->unsignedTinyInteger('shooting')->nullable();
            $table->unsignedTinyInteger('passing')->nullable();
            $table->unsignedTinyInteger('dribbling')->nullable();
            $table->unsignedTinyInteger('defending')->nullable();
            $table->unsignedTinyInteger('physicality')->nullable();

            // Detaylı Özellikler
            $table->unsignedTinyInteger('finishing')->nullable();
            $table->unsignedTinyInteger('heading_accuracy')->nullable();
            $table->unsignedTinyInteger('free_kick_accuracy')->nullable();
            $table->unsignedTinyInteger('shot_power')->nullable();
            $table->unsignedTinyInteger('long_shots')->nullable();
            $table->unsignedTinyInteger('vision')->nullable();
            $table->unsignedTinyInteger('crossing')->nullable();
            $table->unsignedTinyInteger('ball_control')->nullable();
            $table->unsignedTinyInteger('agility')->nullable();
            $table->unsignedTinyInteger('stamina')->nullable();
            $table->unsignedTinyInteger('strength')->nullable();
            $table->unsignedTinyInteger('aggression')->nullable();
            $table->unsignedTinyInteger('positioning')->nullable();

            // Zihinsel
            $table->unsignedTinyInteger('composure')->nullable();
            $table->unsignedTinyInteger('work_rate_attack')->nullable();
            $table->unsignedTinyInteger('work_rate_defense')->nullable();

            $table->text('strong_foot')->nullable(); // "Sol ayak", "Her iki ayak"
            $table->json('strengths')->nullable(); // ["Hız", "Şut Gücü"]
            $table->json('weaknesses')->nullable(); // ["Zayıf ayak", "Pas"]

            $table->timestamps();

            $table->unique('player_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_attributes');
        Schema::dropIfExists('league_standings');
        Schema::dropIfExists('player_comparisons');
        Schema::dropIfExists('transfer_rumors');
        Schema::dropIfExists('club_market_values');
        Schema::dropIfExists('match_player_stats');
        Schema::dropIfExists('match_details');
        Schema::dropIfExists('player_detailed_statistics');
    }
};
