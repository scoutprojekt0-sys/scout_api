<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Oyuncu performans kayıtları
        Schema::create('player_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('season', 20); // "2025-2026"
            $table->string('competition', 100)->nullable(); // Liga, Kupa vs.
            $table->unsignedSmallInteger('matches_played')->default(0);
            $table->unsignedSmallInteger('goals')->default(0);
            $table->unsignedSmallInteger('assists')->default(0);
            $table->unsignedSmallInteger('yellow_cards')->default(0);
            $table->unsignedSmallInteger('red_cards')->default(0);
            $table->unsignedInteger('minutes_played')->default(0);
            $table->decimal('rating', 3, 1)->nullable(); // 0.0-10.0
            $table->timestamps();

            $table->unique(['player_user_id', 'season', 'competition']);
        });

        // Takım anlaşmaları/sözleşmeler
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('team_user_id')->constrained('users')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('salary', 10, 2)->nullable(); // Aylık maaş
            $table->enum('status', ['active', 'expired', 'terminated'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['player_user_id', 'status']);
            $table->index(['team_user_id', 'status']);
        });

        // Scout raporları
        Schema::create('scout_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scout_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 160);
            $table->text('technical_assessment')->nullable(); // Teknik yetenek
            $table->text('physical_assessment')->nullable(); // Fiziksel özellikler
            $table->text('mental_assessment')->nullable(); // Zihinsel/karakter
            $table->unsignedTinyInteger('overall_rating')->nullable(); // 1-100
            $table->enum('recommendation', ['highly_recommended', 'recommended', 'neutral', 'not_recommended'])->default('neutral');
            $table->date('watched_date')->nullable(); // İzleme tarihi
            $table->string('watched_location', 120)->nullable(); // Nerede izlendi
            $table->boolean('is_private')->default(false); // Özel rapor
            $table->timestamps();

            $table->index(['scout_user_id', 'player_user_id']);
        });

        // Kullanıcı aktivite logları
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action', 100); // "profile_viewed", "video_uploaded", etc.
            $table->string('entity_type', 50)->nullable(); // "User", "Opportunity", etc.
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('metadata')->nullable(); // Ek bilgiler
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
        });

        // Kullanıcı engelleme sistemi
        Schema::create('user_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blocker_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('blocked_user_id')->constrained('users')->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->unique(['blocker_user_id', 'blocked_user_id']);
        });

        // Profil görüntüleme takibi
        Schema::create('profile_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viewer_user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('viewed_user_id')->constrained('users')->cascadeOnDelete();
            $table->ipAddress('ip_address')->nullable(); // Anonim görüntülemeler için
            $table->timestamp('viewed_at')->useCurrent();

            $table->index(['viewed_user_id', 'viewed_at']);
            $table->index(['viewer_user_id', 'viewed_at']);
        });

        // Kullanıcı doğrulama (verification)
        Schema::create('user_verifications', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->cascadeOnDelete();
            $table->boolean('email_verified')->default(false);
            $table->boolean('phone_verified')->default(false);
            $table->boolean('identity_verified')->default(false); // Kimlik doğrulama
            $table->string('verification_document_url')->nullable();
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_verifications');
        Schema::dropIfExists('profile_views');
        Schema::dropIfExists('user_blocks');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('scout_reports');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('player_statistics');
    }
};
