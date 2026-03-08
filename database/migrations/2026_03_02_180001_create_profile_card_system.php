<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Profil Kartı Görünümü - Futbolcu
        Schema::create('player_profile_card', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Temel Bilgi
            $table->string('full_name', 150);
            $table->unsignedSmallInteger('age')->nullable();
            $table->enum('position', [
                'goalkeeper',
                'defender',
                'midfielder',
                'forward',
                'unknown'
            ])->default('unknown');

            // Fiziksel Özellikler
            $table->unsignedSmallInteger('height')->nullable(); // cm
            $table->unsignedSmallInteger('weight')->nullable(); // kg
            $table->enum('preferred_foot', ['left', 'right', 'both'])->default('right');

            // Görsel
            $table->string('profile_photo_url', 500)->nullable();
            $table->string('banner_photo_url', 500)->nullable();
            $table->json('gallery_photos')->nullable(); // 3-5 fotoğraf

            // Video
            $table->string('main_video_url', 500)->nullable(); // YouTube, Vimeo vb
            $table->unsignedSmallInteger('video_duration')->nullable(); // saniye
            $table->json('other_videos')->nullable(); // 2-3 ek video

            // İstatistikler (Kart Seviyesi)
            $table->unsignedSmallInteger('goals')->default(0);
            $table->unsignedSmallInteger('assists')->default(0);
            $table->unsignedSmallInteger('matches_played')->default(0);

            // Rating
            $table->decimal('overall_rating', 3, 1)->nullable(); // 1-100
            $table->unsignedSmallInteger('viewers_count')->default(0); // Kim baktı
            $table->unsignedSmallInteger('favorites_count')->default(0); // Kaç kişi favori etti

            // Sosyal
            $table->json('social_links')->nullable(); // Instagram, Twitter, vb

            // Durum
            $table->boolean('is_public')->default(true);
            $table->boolean('is_verified')->default(false);

            $table->timestamps();

            $table->unique('user_id');
        });

        // Profil Kartı - Menajer
        Schema::create('manager_profile_card', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Temel Bilgi
            $table->string('full_name', 150);
            $table->unsignedSmallInteger('age')->nullable();
            $table->string('current_team', 150)->nullable();
            $table->enum('specialization', [
                'youth_development',
                'tactical',
                'fitness',
                'scouting',
                'general',
                'other'
            ])->default('general');

            // Görsel
            $table->string('profile_photo_url', 500)->nullable();
            $table->string('banner_photo_url', 500)->nullable();
            $table->json('gallery_photos')->nullable();

            // Video
            $table->string('intro_video_url', 500)->nullable(); // Tanıtım videosu
            $table->json('coaching_videos')->nullable(); // Antrenman videoları

            // İstatistikler
            $table->unsignedSmallInteger('years_experience')->nullable();
            $table->unsignedSmallInteger('teams_managed')->default(0);
            $table->unsignedSmallInteger('players_developed')->default(0);
            $table->decimal('win_rate', 5, 2)->nullable();

            // Rating
            $table->decimal('overall_rating', 3, 1)->nullable();
            $table->unsignedSmallInteger('viewers_count')->default(0);
            $table->unsignedSmallInteger('followers_count')->default(0);

            // Sosyal
            $table->json('social_links')->nullable();

            // Durum
            $table->boolean('is_public')->default(true);
            $table->boolean('is_verified')->default(false);

            $table->timestamps();

            $table->unique('user_id');
        });

        // Profil Kartı - Antrenör
        Schema::create('coach_profile_card', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Temel Bilgi
            $table->string('full_name', 150);
            $table->unsignedSmallInteger('age')->nullable();
            $table->string('current_team', 150)->nullable();
            $table->enum('coaching_area', [
                'goalkeeper_coach',
                'defensive_coach',
                'offensive_coach',
                'fitness_coach',
                'mental_coach',
                'other'
            ])->default('other');

            // Sertifikalar
            $table->json('certifications')->nullable(); // UEFA, FIFA, vb
            $table->json('languages')->nullable();

            // Görsel
            $table->string('profile_photo_url', 500)->nullable();
            $table->string('banner_photo_url', 500)->nullable();
            $table->json('gallery_photos')->nullable();

            // Video
            $table->string('coaching_technique_video', 500)->nullable();
            $table->json('training_session_videos')->nullable();

            // İstatistikler
            $table->unsignedSmallInteger('years_experience')->nullable();
            $table->unsignedSmallInteger('players_trained')->default(0);
            $table->decimal('success_rate', 5, 2)->nullable();

            // Rating
            $table->decimal('overall_rating', 3, 1)->nullable();
            $table->unsignedSmallInteger('viewers_count')->default(0);
            $table->unsignedSmallInteger('followers_count')->default(0);

            // Sosyal
            $table->json('social_links')->nullable();

            // Durum
            $table->boolean('is_public')->default(true);
            $table->boolean('is_verified')->default(false);

            $table->timestamps();

            $table->unique('user_id');
        });

        // Scout Abilir - Kim profili gördü
        Schema::create('profile_card_views', function (Blueprint $table) {
            $table->id();
            $table->enum('card_type', ['player', 'manager', 'coach']);
            $table->foreignId('card_owner_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('viewer_user_id')->constrained('users')->cascadeOnDelete();

            // Bakış Detayları
            $table->dateTime('viewed_at');
            $table->unsignedSmallInteger('view_duration_seconds')->default(0);
            $table->enum('view_type', ['partial', 'full', 'deep'])->default('partial');

            // Hangi bölümler görüldü
            $table->boolean('viewed_photos')->default(false);
            $table->boolean('viewed_videos')->default(false);
            $table->boolean('viewed_stats')->default(false);

            $table->timestamps();

            $table->index(['card_owner_user_id', 'card_type']);
        });

        // Profil Kartı Beğeni/Yorum
        Schema::create('profile_card_interactions', function (Blueprint $table) {
            $table->id();
            $table->enum('card_type', ['player', 'manager', 'coach']);
            $table->foreignId('card_owner_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // İnteraksiyon Türü
            $table->enum('interaction_type', ['like', 'comment', 'share', 'save'])->default('like');

            // Yorum
            $table->text('comment')->nullable();
            $table->decimal('rating', 3, 1)->nullable(); // 1-5

            // Referans
            $table->string('reference', 100)->nullable(); // "Great potential", "Professional", vb

            $table->timestamps();

            $table->index(['card_owner_user_id', 'card_type']);
        });

        // Profil Kartı Tasarım Tercihleri
        Schema::create('profile_card_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Tema
            $table->enum('theme', ['light', 'dark', 'gradient', 'minimalist'])->default('gradient');
            $table->string('primary_color', 7)->default('#2563EB'); // Hex renk
            $table->string('secondary_color', 7)->default('#7C3AED');

            // Düzen
            $table->enum('layout', ['modern', 'classic', 'artistic', 'minimal'])->default('modern');
            $table->boolean('show_social_links')->default(true);
            $table->boolean('show_statistics')->default(true);
            $table->boolean('show_video_highlight')->default(true);

            // Gizlilik
            $table->boolean('allow_messages')->default(true);
            $table->boolean('show_view_count')->default(true);

            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_card_settings');
        Schema::dropIfExists('profile_card_interactions');
        Schema::dropIfExists('profile_card_views');
        Schema::dropIfExists('coach_profile_card');
        Schema::dropIfExists('manager_profile_card');
        Schema::dropIfExists('player_profile_card');
    }
};
