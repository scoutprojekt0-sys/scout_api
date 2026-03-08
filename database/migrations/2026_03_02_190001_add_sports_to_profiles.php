<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Oyuncuya Spor Dalı Alanı Ekle
        Schema::table('player_profile_card', function (Blueprint $table) {
            $table->enum('sport', ['football', 'basketball', 'volleyball'])->default('football')->after('position');
            $table->string('sport_level', 100)->nullable()->after('sport'); // Professional, Amateur, Youth
            $table->index('sport');
        });

        // Antrenöre Spor Dalı Alanları Ekle
        Schema::table('coach_profile_card', function (Blueprint $table) {
            $table->json('sports')->default('["football"]')->after('coaching_area'); // Hangi sporlarla çalışıyor
            $table->string('primary_sport', 50)->default('football')->after('sports'); // Ana spor
            $table->index('primary_sport');
        });

        // Oyuncu Profil Kartına Spor İstatistikleri Ekle
        Schema::table('player_profile_card', function (Blueprint $table) {
            $table->unsignedSmallInteger('basketball_points')->nullable()->after('matches_played'); // Basketbol puanları
            $table->unsignedSmallInteger('basketball_rebounds')->nullable()->after('basketball_points'); // Ribaund
            $table->unsignedSmallInteger('basketball_assists')->nullable()->after('basketball_rebounds'); // Asist

            $table->unsignedSmallInteger('volleyball_kills')->nullable()->after('basketball_assists'); // Voleybol kill
            $table->unsignedSmallInteger('volleyball_blocks')->nullable()->after('volleyball_kills'); // Blok
            $table->unsignedSmallInteger('volleyball_aces')->nullable()->after('volleyball_blocks'); // As
        });

        // Antrenörlere Spor Bazlı İstatistikler Ekle
        Schema::table('coach_profile_card', function (Blueprint $table) {
            $table->json('sports_experience')->nullable()->after('primary_sport'); // Her spor için deneyim
            // Format: {"football": {"years": 10, "teams": 5}, "basketball": {"years": 5, "teams": 2}}
        });
    }

    public function down(): void
    {
        Schema::table('player_profile_card', function (Blueprint $table) {
            $table->dropIndex(['sport']);
            $table->dropColumn(['sport', 'sport_level', 'basketball_points', 'basketball_rebounds', 'basketball_assists', 'volleyball_kills', 'volleyball_blocks', 'volleyball_aces']);
        });

        Schema::table('coach_profile_card', function (Blueprint $table) {
            $table->dropIndex(['primary_sport']);
            $table->dropColumn(['sports', 'primary_sport', 'sports_experience']);
        });
    }
};
