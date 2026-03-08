<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Gamification: User badges/achievements
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->enum('type', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze');
            $table->integer('points')->default(0);
            $table->json('criteria')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('achievement_id')->constrained()->cascadeOnDelete();
            $table->timestamp('earned_at');
            $table->integer('progress')->default(100);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'achievement_id']);
            $table->index('earned_at');
        });

        // Gamification: XP and Levels
        Schema::create('user_xp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('xp_amount');
            $table->string('action_type', 80);
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('xp_points')->default(0)->after('phone');
            $table->integer('level')->default(1)->after('xp_points');
            $table->integer('coins')->default(0)->after('level');
        });

        // Community: Posts/Feed
        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->enum('type', ['text', 'image', 'video', 'poll', 'share'])->default('text');
            $table->json('media')->nullable();
            $table->json('poll_data')->nullable();
            $table->foreignId('shared_post_id')->nullable()->constrained('community_posts')->nullOnDelete();
            $table->enum('visibility', ['public', 'followers', 'private'])->default('public');
            $table->boolean('is_pinned')->default(false);
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'created_at']);
            $table->index('visibility');
        });

        Schema::create('community_post_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained('community_posts')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'post_id']);
        });

        Schema::create('community_post_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained('community_posts')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('community_post_comments')->cascadeOnDelete();
            $table->text('content');
            $table->integer('likes_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['post_id', 'created_at']);
        });

        // Community: Follow system
        Schema::create('follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('following_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('followed_at');

            $table->unique(['follower_id', 'following_id']);
            $table->index('follower_id');
            $table->index('following_id');
        });

        // Virality: Referrals
        if (!Schema::hasTable('referrals')) {
            Schema::create('referrals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('referred_id')->constrained('users')->cascadeOnDelete();
                $table->string('referral_code', 50);
                $table->integer('reward_xp')->default(0);
                $table->integer('reward_coins')->default(0);
                $table->boolean('reward_claimed')->default(false);
                $table->timestamp('claimed_at')->nullable();
                $table->timestamps();

                $table->index('referral_code');
                $table->unique('referred_id');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code', 50)->unique()->nullable()->after('coins');
        });

        // Virality: Viral content tracking
        Schema::create('viral_contents', function (Blueprint $table) {
            $table->id();
            $table->morphs('contentable');
            $table->integer('shares_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->integer('viral_score')->default(0);
            $table->timestamp('viral_detected_at')->nullable();
            $table->timestamps();

            $table->index('viral_score');
        });

        // Real-time: Notifications
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('type', 80);
                $table->string('title', 150);
                $table->text('message');
                $table->json('data')->nullable();
                $table->string('action_url', 255)->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'is_read', 'created_at']);
            });
        }

        // Real-time: Online status
        Schema::create('user_online_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_online')->default(false);
            $table->timestamp('last_seen_at');
            $table->string('device_type', 50)->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->index(['is_online', 'last_seen_at']);
        });

        // AI: User preferences for recommendations
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('preferred_positions')->nullable();
            $table->json('preferred_leagues')->nullable();
            $table->json('preferred_countries')->nullable();
            $table->json('age_range')->nullable();
            $table->json('budget_range')->nullable();
            $table->json('custom_filters')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });

        // AI: Recommendation logs
        Schema::create('ai_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('recommendable');
            $table->float('score', 8, 4);
            $table->json('factors')->nullable();
            $table->boolean('clicked')->default(false);
            $table->boolean('saved')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_recommendations');
        Schema::dropIfExists('user_preferences');
        Schema::dropIfExists('user_online_status');
        // Shared notifications table may be owned by another migration.
        Schema::dropIfExists('viral_contents');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('referral_code');
        });
        // Shared referrals table may be owned by another migration.
        Schema::dropIfExists('follows');
        Schema::dropIfExists('community_post_comments');
        Schema::dropIfExists('community_post_likes');
        Schema::dropIfExists('community_posts');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['xp_points', 'level', 'coins']);
        });
        Schema::dropIfExists('user_xp_logs');
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievements');
    }
};
