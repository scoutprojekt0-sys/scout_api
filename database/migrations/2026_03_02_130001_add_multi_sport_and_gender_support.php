<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tüm tablolara spor ve cinsiyet desteği ekle

        // Players profil tablosunu geliştir
        Schema::table('player_profiles', function (Blueprint $table) {
            $table->enum('sport', ['football', 'basketball', 'volleyball'])->default('football')->after('position');
            $table->enum('gender', ['male', 'female', 'all'])->default('male')->after('sport');
        });

        // Amatör takımlar
        Schema::table('amateur_teams', function (Blueprint $table) {
            $table->enum('sport', ['football', 'basketball', 'volleyball'])->default('football')->after('team_type');
            $table->enum('team_gender', ['male', 'female', 'mixed'])->default('male')->after('sport');
        });

        // Amatör ligler
        Schema::table('amateur_leagues', function (Blueprint $table) {
            $table->enum('sport', ['football', 'basketball', 'volleyball'])->default('football')->after('type');
            $table->enum('league_gender', ['male', 'female', 'mixed'])->default('male')->after('sport');
        });

        // Serbest oyuncu ilanları
        Schema::table('free_agent_listings', function (Blueprint $table) {
            $table->enum('sport', ['football', 'basketball', 'volleyball'])->default('football')->after('title');
            $table->enum('player_gender', ['male', 'female', 'any'])->default('any')->after('sport');
        });

        // Topluluk etkinlikleri
        Schema::table('community_events', function (Blueprint $table) {
            $table->enum('sport', ['football', 'basketball', 'volleyball'])->default('football')->after('event_type');
            $table->enum('event_gender', ['male', 'female', 'mixed'])->default('mixed')->after('sport');
        });

        // Video portföy
        Schema::table('player_video_portfolio', function (Blueprint $table) {
            $table->enum('sport', ['football', 'basketball', 'volleyball'])->default('football')->after('video_type');
        });

        // Deneme talepleri
        Schema::table('trial_requests', function (Blueprint $table) {
            $table->enum('sport', ['football', 'basketball', 'volleyball'])->default('football')->after('request_type');
        });

        // Pozisyonlar tablosunu geliştir
        Schema::table('positions', function (Blueprint $table) {
            $table->enum('sport', ['football', 'basketball', 'volleyball'])->default('football')->after('category');
        });

        // Yeni spor-specific liglerle ilişkili tablo
        Schema::create('sports_types', function (Blueprint $table) {
            $table->id();
            $table->enum('name', ['football', 'basketball', 'volleyball']);
            $table->string('display_name', 50); // "Futbol", "Basketbol", "Voleybol"
            $table->string('icon_url')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique('name');
        });

        // Cinsiyet tercih tablosu
        Schema::create('gender_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('preferred_sport', ['football', 'basketball', 'volleyball'])->nullable();
            $table->enum('preferred_gender_to_play_with', ['male', 'female', 'mixed', 'no_preference'])->default('no_preference');
            $table->boolean('comfortable_mixed_team')->default(true);
            $table->timestamps();

            $table->unique('user_id');
        });

        // Spor-specific istatistikler (genişletilmiş)
        Schema::create('sport_specific_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('sport', ['football', 'basketball', 'volleyball']);

            // Futbol istatistikleri
            $table->unsignedSmallInteger('football_goals')->default(0);
            $table->unsignedSmallInteger('football_assists')->default(0);

            // Basketbol istatistikleri
            $table->unsignedSmallInteger('basketball_points')->default(0);
            $table->unsignedSmallInteger('basketball_rebounds')->default(0);
            $table->unsignedSmallInteger('basketball_assists')->default(0);
            $table->unsignedSmallInteger('basketball_steals')->default(0);

            // Voleybol istatistikleri
            $table->unsignedSmallInteger('volleyball_aces')->default(0);
            $table->unsignedSmallInteger('volleyball_kills')->default(0);
            $table->unsignedSmallInteger('volleyball_blocks')->default(0);
            $table->unsignedSmallInteger('volleyball_digs')->default(0);

            $table->timestamps();

            $table->unique(['player_user_id', 'sport']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sport_specific_stats');
        Schema::dropIfExists('gender_preferences');
        Schema::dropIfExists('sports_types');

        Schema::table('trial_requests', function (Blueprint $table) {
            $table->dropColumn('sport');
        });

        Schema::table('player_video_portfolio', function (Blueprint $table) {
            $table->dropColumn('sport');
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('sport');
        });

        Schema::table('community_events', function (Blueprint $table) {
            $table->dropColumn(['sport', 'event_gender']);
        });

        Schema::table('free_agent_listings', function (Blueprint $table) {
            $table->dropColumn(['sport', 'player_gender']);
        });

        Schema::table('amateur_leagues', function (Blueprint $table) {
            $table->dropColumn(['sport', 'league_gender']);
        });

        Schema::table('amateur_teams', function (Blueprint $table) {
            $table->dropColumn(['sport', 'team_gender']);
        });

        Schema::table('player_profiles', function (Blueprint $table) {
            $table->dropColumn(['sport', 'gender']);
        });
    }
};
