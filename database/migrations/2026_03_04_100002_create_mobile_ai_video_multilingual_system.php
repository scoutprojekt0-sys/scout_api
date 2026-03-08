<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Multilingual content support
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 10);
            $table->morphs('translatable');
            $table->string('field', 100);
            $table->text('value');
            $table->timestamps();

            $table->unique(['translatable_type', 'translatable_id', 'field', 'locale'], 'translations_unique');
            $table->index('locale');
        });

        // Mobile: Device tokens for push notifications
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token', 255);
            $table->enum('platform', ['ios', 'android', 'web'])->default('web');
            $table->string('device_name', 150)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at');
            $table->timestamps();

            $table->index(['user_id', 'platform']);
            $table->unique('token');
        });

        // Mobile: App versions tracking
        Schema::create('mobile_app_versions', function (Blueprint $table) {
            $table->id();
            $table->enum('platform', ['ios', 'android']);
            $table->string('version', 20);
            $table->integer('build_number');
            $table->boolean('is_required')->default(false);
            $table->text('release_notes')->nullable();
            $table->string('download_url', 255)->nullable();
            $table->timestamp('released_at');
            $table->timestamps();

            $table->unique(['platform', 'version']);
        });

        // Video: CDN-ready video storage
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('filename', 255);
            $table->string('original_path', 500);
            $table->string('cdn_url', 500)->nullable();
            $table->string('thumbnail_url', 500)->nullable();
            $table->json('transcoded_urls')->nullable();
            $table->integer('duration_seconds')->default(0);
            $table->bigInteger('file_size')->default(0);
            $table->string('mime_type', 50)->nullable();
            $table->enum('status', ['uploading', 'processing', 'ready', 'failed'])->default('uploading');
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->enum('visibility', ['public', 'private', 'unlisted'])->default('public');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index('visibility');
        });

        // AI: Player similarity/matching
        Schema::create('player_similarities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_a_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('player_b_id')->constrained('users')->cascadeOnDelete();
            $table->float('similarity_score', 8, 4);
            $table->json('matching_attributes')->nullable();
            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->unique(['player_a_id', 'player_b_id']);
            $table->index('similarity_score');
        });

        // AI: Smart search logs
        Schema::create('ai_search_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('query');
            $table->json('filters')->nullable();
            $table->integer('results_count')->default(0);
            $table->json('top_results')->nullable();
            $table->boolean('has_clicks')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // WebSocket: Real-time events queue
        Schema::create('realtime_events', function (Blueprint $table) {
            $table->id();
            $table->string('channel', 150);
            $table->string('event_type', 100);
            $table->json('payload');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['channel', 'is_processed']);
            $table->index('created_at');
        });

        // Localization: User language preferences
        Schema::table('users', function (Blueprint $table) {
            $table->string('locale', 10)->default('tr')->after('referral_code');
            $table->string('timezone', 50)->default('Europe/Istanbul')->after('locale');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['locale', 'timezone']);
        });
        Schema::dropIfExists('realtime_events');
        Schema::dropIfExists('ai_search_logs');
        Schema::dropIfExists('player_similarities');
        Schema::dropIfExists('videos');
        Schema::dropIfExists('mobile_app_versions');
        Schema::dropIfExists('device_tokens');
        Schema::dropIfExists('translations');
    }
};
