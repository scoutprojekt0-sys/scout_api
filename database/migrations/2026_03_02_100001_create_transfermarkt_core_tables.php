<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ülkeler
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 3)->unique(); // TUR, GER, ENG
            $table->string('flag_url')->nullable();
            $table->string('fifa_code', 3)->nullable();
            $table->timestamps();
        });

        // Ligler
        Schema::create('leagues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('short_name', 50)->nullable();
            $table->enum('tier', ['1', '2', '3', '4', '5', 'other'])->default('1');
            $table->string('logo_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('team_count')->default(18);
            $table->timestamps();
        });

        // Sezonlar
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20); // "2025-2026"
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });

        // Kulüpler (Professional Teams)
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('league_id')->nullable()->constrained('leagues')->nullOnDelete();
            $table->string('name', 140);
            $table->string('short_name', 50)->nullable();
            $table->string('nickname', 100)->nullable();
            $table->string('logo_url')->nullable();
            $table->string('stadium_name', 120)->nullable();
            $table->unsignedInteger('stadium_capacity')->nullable();
            $table->string('city', 80)->nullable();
            $table->unsignedSmallInteger('founded_year')->nullable();
            $table->string('club_colors', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('website_url')->nullable();
            $table->decimal('total_market_value', 15, 2)->default(0); // Toplam kadro değeri
            $table->unsignedInteger('national_team_players')->default(0);
            $table->decimal('average_age', 4, 2)->nullable();
            $table->unsignedSmallInteger('foreigner_count')->default(0);
            $table->timestamps();

            $table->index(['league_id', 'country_id']);
        });

        // Pozisyonlar (Detaylı)
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50); // "Merkez Forvet", "Sol Bek"
            $table->string('short_name', 10); // "CF", "LB"
            $table->enum('category', ['goalkeeper', 'defender', 'midfielder', 'forward']);
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Transfer Değerleri (Piyasa Değeri Geçmişi)
        Schema::create('player_market_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('market_value', 12, 2); // Euro cinsinden
            $table->date('valuation_date');
            $table->string('currency', 3)->default('EUR');
            $table->text('change_reason')->nullable(); // "İyi performans", "Yaralanma"
            $table->timestamps();

            $table->index(['player_user_id', 'valuation_date']);
        });

        // Transfer İşlemleri
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('from_club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->foreignId('to_club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->foreignId('season_id')->nullable()->constrained('seasons')->nullOnDelete();
            $table->date('transfer_date');
            $table->enum('transfer_type', ['transfer', 'loan', 'free', 'end_of_loan', 'retirement'])->default('transfer');
            $table->decimal('transfer_fee', 12, 2)->nullable(); // Transfer ücreti
            $table->string('currency', 3)->default('EUR');
            $table->decimal('market_value_at_time', 12, 2)->nullable(); // Transfer anındaki piyasa değeri
            $table->text('notes')->nullable();
            $table->boolean('is_confirmed')->default(false);
            $table->date('loan_end_date')->nullable(); // Kiralık ise
            $table->boolean('option_to_buy')->default(false);
            $table->timestamps();

            $table->index(['player_user_id', 'transfer_date']);
            $table->index(['from_club_id', 'to_club_id']);
        });

        // Milli Takım Bilgileri
        Schema::create('national_team_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->enum('team_type', ['senior', 'u21', 'u19', 'u17', 'u15'])->default('senior');
            $table->unsignedSmallInteger('caps')->default(0); // Forma sayısı
            $table->unsignedSmallInteger('goals')->default(0);
            $table->date('debut_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['player_user_id', 'country_id', 'team_type']);
        });

        // Yaralanmalar
        Schema::create('injuries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('injury_type', 100); // "Bağ sakatlığı", "Kırık"
            $table->enum('severity', ['minor', 'moderate', 'severe', 'career_threatening'])->default('moderate');
            $table->date('injury_date');
            $table->date('expected_return_date')->nullable();
            $table->date('actual_return_date')->nullable();
            $table->unsignedSmallInteger('days_out')->nullable();
            $table->unsignedSmallInteger('games_missed')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'recovered', 'recurring'])->default('active');
            $table->timestamps();

            $table->index(['player_user_id', 'injury_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('injuries');
        Schema::dropIfExists('national_team_players');
        Schema::dropIfExists('transfers');
        Schema::dropIfExists('player_market_values');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('clubs');
        Schema::dropIfExists('seasons');
        Schema::dropIfExists('leagues');
        Schema::dropIfExists('countries');
    }
};
