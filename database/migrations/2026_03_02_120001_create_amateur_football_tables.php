<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Amatör Ligler ve Turnuvalar
        Schema::create('amateur_leagues', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->enum('type', ['local_league', 'regional_league', 'tournament', 'cup', 'friendly'])->default('local_league');
            $table->enum('level', ['district', 'city', 'regional', 'amateur_national'])->default('city');
            $table->string('city', 80)->nullable();
            $table->string('district', 80)->nullable();
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['upcoming', 'active', 'completed', 'cancelled'])->default('upcoming');
            $table->string('organizer', 120)->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->string('contact_email', 120)->nullable();
            $table->unsignedSmallInteger('team_capacity')->nullable();
            $table->unsignedSmallInteger('registered_teams')->default(0);
            $table->decimal('entry_fee', 8, 2)->default(0);
            $table->boolean('is_free')->default(true);
            $table->timestamps();

            $table->index(['city', 'type', 'status']);
        });

        // Amatör Takımlar (Küçük kulüpler, mahalle takımları)
        Schema::create('amateur_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Takım kaptanı/yöneticisi
            $table->string('team_name', 140);
            $table->enum('team_type', ['club', 'neighborhood', 'workplace', 'university', 'school', 'friends'])->default('neighborhood');
            $table->string('city', 80);
            $table->string('district', 80)->nullable();
            $table->string('neighborhood', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('home_field', 120)->nullable(); // Saha adı
            $table->enum('field_type', ['grass', 'artificial', 'halısaha', 'concrete'])->nullable();
            $table->string('practice_days', 100)->nullable(); // "Salı, Perşembe"
            $table->string('practice_time', 50)->nullable(); // "19:00-21:00"
            $table->unsignedTinyInteger('current_players')->default(0);
            $table->unsignedTinyInteger('needed_players')->default(0);
            $table->json('needed_positions')->nullable(); // ["Kaleci", "Forvet"]
            $table->decimal('monthly_fee', 8, 2)->nullable(); // Aylık aidat
            $table->boolean('accepts_new_players')->default(true);
            $table->string('contact_phone', 30)->nullable();
            $table->string('whatsapp_group')->nullable();
            $table->timestamps();

            $table->index(['city', 'district', 'accepts_new_players']);
        });

        // Deneme Maçı/Antrenman Talepleri
        Schema::create('trial_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('amateur_teams')->cascadeOnDelete();
            $table->enum('request_type', ['trial_match', 'training', 'friendly_match'])->default('trial_match');
            $table->text('message')->nullable();
            $table->date('preferred_date')->nullable();
            $table->string('preferred_time', 50)->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'completed'])->default('pending');
            $table->text('team_response')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->text('feedback')->nullable(); // Takımdan geri bildirim
            $table->unsignedTinyInteger('performance_rating')->nullable(); // 1-10
            $table->boolean('offered_position')->default(false);
            $table->timestamps();

            $table->index(['player_user_id', 'status']);
            $table->index(['team_id', 'status']);
        });

        // Takım Arayışları (Oyuncu Arayan Takımlar)
        Schema::create('team_player_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('amateur_teams')->cascadeOnDelete();
            $table->foreignId('posted_by')->constrained('users')->cascadeOnDelete();
            $table->string('title', 160);
            $table->json('positions_needed'); // ["Kaleci", "Stoper", "Forvet"]
            $table->unsignedTinyInteger('players_needed')->default(1);
            $table->unsignedTinyInteger('min_age')->nullable();
            $table->unsignedTinyInteger('max_age')->nullable();
            $table->enum('skill_level', ['beginner', 'intermediate', 'advanced', 'any'])->default('any');
            $table->text('requirements')->nullable();
            $table->decimal('monthly_fee', 8, 2)->nullable();
            $table->boolean('transportation_provided')->default(false);
            $table->boolean('equipment_provided')->default(false);
            $table->enum('commitment_level', ['casual', 'regular', 'competitive'])->default('regular');
            $table->enum('status', ['active', 'filled', 'closed'])->default('active');
            $table->timestamps();

            $table->index(['team_id', 'status']);
        });

        // Serbest Oyuncu İlanları
        Schema::create('free_agent_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 160);
            $table->json('preferred_positions'); // ["Forvet", "Kanat"]
            $table->string('city', 80);
            $table->string('district', 80)->nullable();
            $table->enum('availability', ['immediately', 'next_season', 'flexible'])->default('flexible');
            $table->json('available_days')->nullable(); // ["Salı", "Perşembe", "Cumartesi"]
            $table->string('available_time', 50)->nullable(); // "Akşam 19:00 sonrası"
            $table->enum('skill_level', ['beginner', 'intermediate', 'advanced'])->default('intermediate');
            $table->decimal('max_monthly_fee', 8, 2)->nullable(); // Ödeyebileceği max aidat
            $table->boolean('has_equipment')->default(true);
            $table->boolean('has_transportation')->default(true);
            $table->text('about')->nullable();
            $table->text('experience')->nullable();
            $table->enum('status', ['active', 'found_team', 'inactive'])->default('active');
            $table->timestamps();

            $table->index(['city', 'status']);
            $table->index(['player_user_id', 'status']);
        });

        // Maç Kayıtları (Amatör Maçlar)
        Schema::create('amateur_match_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->nullable()->constrained('amateur_leagues')->nullOnDelete();
            $table->foreignId('home_team_id')->constrained('amateur_teams')->cascadeOnDelete();
            $table->foreignId('away_team_id')->constrained('amateur_teams')->cascadeOnDelete();
            $table->date('match_date');
            $table->string('venue', 120)->nullable();
            $table->unsignedTinyInteger('home_score')->nullable();
            $table->unsignedTinyInteger('away_score')->nullable();
            $table->enum('match_type', ['league', 'tournament', 'friendly', 'cup'])->default('friendly');
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'postponed'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['match_date', 'status']);
        });

        // Oyuncu Performans Notları (Basit)
        Schema::create('player_match_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('amateur_match_records')->cascadeOnDelete();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('amateur_teams')->cascadeOnDelete();
            $table->boolean('played')->default(true);
            $table->unsignedTinyInteger('goals')->default(0);
            $table->unsignedTinyInteger('assists')->default(0);
            $table->unsignedTinyInteger('yellow_cards')->default(0);
            $table->unsignedTinyInteger('red_cards')->default(0);
            $table->unsignedTinyInteger('rating')->nullable(); // 1-10
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['player_user_id', 'match_id']);
        });

        // Video Portföy (Amatörler için önemli)
        Schema::create('player_video_portfolio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 160);
            $table->text('description')->nullable();
            $table->string('video_url'); // YouTube, Vimeo, etc.
            $table->string('thumbnail_url')->nullable();
            $table->enum('video_type', ['highlights', 'full_match', 'training', 'skills', 'goals'])->default('highlights');
            $table->date('recorded_date')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('likes')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->index(['player_user_id', 'is_public']);
        });

        // Referanslar/Tavsiyeler
        Schema::create('player_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('given_by')->constrained('users')->cascadeOnDelete(); // Koç, kaptan, vs.
            $table->enum('reference_type', ['coach', 'team_captain', 'teammate', 'scout'])->default('coach');
            $table->string('referee_name', 120); // Referans veren kişi
            $table->string('referee_position', 100)->nullable(); // "Takım Kaptanı", "Antrenör"
            $table->text('reference_text');
            $table->unsignedTinyInteger('rating')->nullable(); // 1-10
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->index(['player_user_id', 'is_visible']);
        });

        // Topluluk Etkinlikleri
        Schema::create('community_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 160);
            $table->text('description')->nullable();
            $table->enum('event_type', ['pickup_game', 'tournament', 'training_camp', 'social', 'charity'])->default('pickup_game');
            $table->string('city', 80);
            $table->string('district', 80)->nullable();
            $table->string('venue', 120);
            $table->dateTime('event_date');
            $table->unsignedTinyInteger('max_participants')->nullable();
            $table->unsignedTinyInteger('current_participants')->default(0);
            $table->decimal('entry_fee', 8, 2)->default(0);
            $table->boolean('is_free')->default(true);
            $table->enum('skill_level', ['all_levels', 'beginner', 'intermediate', 'advanced'])->default('all_levels');
            $table->string('contact_info', 120)->nullable();
            $table->enum('status', ['upcoming', 'registration_open', 'registration_closed', 'completed', 'cancelled'])->default('upcoming');
            $table->timestamps();

            $table->index(['city', 'event_type', 'status']);
        });

        // Etkinlik Katılımcıları
        Schema::create('event_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('community_events')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['registered', 'confirmed', 'attended', 'cancelled'])->default('registered');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_participants');
        Schema::dropIfExists('community_events');
        Schema::dropIfExists('player_references');
        Schema::dropIfExists('player_video_portfolio');
        Schema::dropIfExists('player_match_performances');
        Schema::dropIfExists('amateur_match_records');
        Schema::dropIfExists('free_agent_listings');
        Schema::dropIfExists('team_player_searches');
        Schema::dropIfExists('trial_requests');
        Schema::dropIfExists('amateur_teams');
        Schema::dropIfExists('amateur_leagues');
    }
};
