<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Club Needs - Kulüplerin ihtiyaçları
        Schema::create('club_needs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('position', 50); // Kaleci, Forvet, vb
            $table->enum('urgency', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('contract_type', ['transfer', 'loan', 'free_agent'])->nullable();
            $table->integer('min_age')->nullable();
            $table->integer('max_age')->nullable();
            $table->decimal('budget_min', 12, 2)->nullable();
            $table->decimal('budget_max', 12, 2)->nullable();
            $table->json('required_skills')->nullable();
            $table->json('preferred_leagues')->nullable();
            $table->json('preferred_countries')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('views_count')->default(0);
            $table->integer('applications_count')->default(0);
            $table->date('deadline')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'urgency']);
            $table->index('position');
        });

        // Trending/Popular Content - En çok tıklananlar
        Schema::create('trending_content', function (Blueprint $table) {
            $table->id();
            $table->morphs('trendable'); // Player, Video, News, vb
            $table->integer('views_today')->default(0);
            $table->integer('views_week')->default(0);
            $table->integer('views_month')->default(0);
            $table->integer('clicks_today')->default(0);
            $table->integer('clicks_week')->default(0);
            $table->integer('clicks_month')->default(0);
            $table->integer('shares_count')->default(0);
            $table->integer('saves_count')->default(0);
            $table->float('trending_score', 8, 2)->default(0);
            $table->timestamp('last_viewed_at')->nullable();
            $table->date('trending_date')->default(DB::raw('CURRENT_DATE'));
            $table->timestamps();

            $table->index(['trending_score', 'trending_date']);
            $table->index('trendable_type');
        });

        // Hot Transfers - Gündemdeki transferler
        Schema::create('hot_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('from_club_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('to_club_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['rumor', 'negotiating', 'agreed', 'completed', 'failed'])->default('rumor');
            $table->decimal('transfer_fee', 12, 2)->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->integer('reliability_score')->default(50); // 0-100
            $table->text('description')->nullable();
            $table->json('sources')->nullable(); // Haber kaynakları
            $table->integer('views_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->timestamp('rumor_started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'reliability_score']);
        });

        // Featured Content - Öne çıkanlar (Editör seçimi)
        Schema::create('featured_content', function (Blueprint $table) {
            $table->id();
            $table->morphs('featurable');
            $table->enum('section', ['homepage', 'players', 'clubs', 'news', 'videos'])->default('homepage');
            $table->integer('priority')->default(0); // Yüksek = önce göster
            $table->string('badge_text', 50)->nullable(); // "Öne Çıkan", "Günün Oyuncusu", vb
            $table->string('badge_color', 7)->default('#3B82F6');
            $table->timestamp('featured_from')->nullable();
            $table->timestamp('featured_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['section', 'priority', 'is_active']);
        });

        // Rising Stars - Yükselen yıldızlar
        Schema::create('rising_stars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->float('growth_score', 8, 2)->default(0); // Büyüme skoru
            $table->integer('scout_interest_increase')->default(0);
            $table->integer('profile_views_increase')->default(0);
            $table->integer('video_views_increase')->default(0);
            $table->json('performance_data')->nullable();
            $table->timestamp('detected_at');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['growth_score', 'is_featured']);
        });

        // Player of the Week/Month
        Schema::create('player_awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('award_type', ['day', 'week', 'month', 'season'])->default('week');
            $table->string('category', 100); // "Haftanın Oyuncusu", "En İyi Kaleci", vb
            $table->integer('votes_count')->default(0);
            $table->float('rating', 4, 2)->nullable();
            $table->text('reason')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['award_type', 'period_end', 'is_active']);
        });

        // Scout Activity Feed - Scout aktiviteleri
        Schema::create('scout_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scout_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('player_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('activity_type', ['view', 'favorite', 'contact', 'report', 'recommend'])->default('view');
            $table->text('note')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('activity_at');
            $table->timestamps();

            $table->index(['scout_user_id', 'activity_at']);
            $table->index('player_user_id');
        });

        // Market Insights - Piyasa analizi
        Schema::create('market_insights', function (Blueprint $table) {
            $table->id();
            $table->string('position', 50);
            $table->string('league', 100)->nullable();
            $table->string('age_group', 20)->nullable(); // "U21", "21-25", vb
            $table->decimal('avg_transfer_fee', 12, 2)->nullable();
            $table->decimal('max_transfer_fee', 12, 2)->nullable();
            $table->decimal('min_transfer_fee', 12, 2)->nullable();
            $table->integer('transfers_count')->default(0);
            $table->json('trending_stats')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamps();

            $table->index(['position', 'league']);
            $table->index(['period_start', 'period_end']);
        });

        // Watchlist Groups - İzleme listeleri
        Schema::create('watchlist_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('color', 7)->default('#3B82F6');
            $table->boolean('is_public')->default(false);
            $table->integer('players_count')->default(0);
            $table->timestamps();

            $table->index('user_id');
        });

        Schema::create('watchlist_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('watchlist_id')->constrained('watchlist_groups')->cascadeOnDelete();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->integer('priority')->default(0);
            $table->timestamps();

            $table->unique(['watchlist_id', 'player_user_id']);
        });

        // Quick Stats Cache - Hızlı istatistikler
        Schema::create('quick_stats_cache', function (Blueprint $table) {
            $table->id();
            $table->string('stat_key', 100)->unique();
            $table->json('stat_data');
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quick_stats_cache');
        Schema::dropIfExists('watchlist_players');
        Schema::dropIfExists('watchlist_groups');
        Schema::dropIfExists('market_insights');
        Schema::dropIfExists('scout_activities');
        Schema::dropIfExists('player_awards');
        Schema::dropIfExists('rising_stars');
        Schema::dropIfExists('featured_content');
        Schema::dropIfExists('hot_transfers');
        Schema::dropIfExists('trending_content');
        Schema::dropIfExists('club_needs');
    }
};
