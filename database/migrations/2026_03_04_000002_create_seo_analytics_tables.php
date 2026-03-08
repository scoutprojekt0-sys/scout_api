<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SEO Meta Tags
        Schema::create('seo_meta', function (Blueprint $table) {
            $table->id();
            $table->string('page_type'); // player_profile, team, match, etc.
            $table->unsignedBigInteger('page_id');
            $table->string('title', 60);
            $table->text('description')->nullable();
            $table->text('keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('og_type', 50)->default('website');
            $table->string('twitter_card', 50)->default('summary_large_image');
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();
            $table->json('schema_markup')->nullable();
            $table->timestamps();

            $table->index(['page_type', 'page_id']);
        });

        // Analytics Events
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('event_name'); // page_view, button_click, profile_view
            $table->string('event_category')->nullable(); // engagement, conversion, navigation
            $table->string('event_action')->nullable();
            $table->string('event_label')->nullable();
            $table->integer('event_value')->nullable();
            $table->string('page_url');
            $table->string('page_title')->nullable();
            $table->string('referrer')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('device_type', 50)->nullable(); // mobile, desktop, tablet
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('city')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->json('custom_data')->nullable();
            $table->timestamp('created_at');

            $table->index('event_name');
            $table->index('user_id');
            $table->index('created_at');
        });

        // Page Views
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('page_type'); // profile, match, team
            $table->unsignedBigInteger('page_id');
            $table->string('session_id', 100);
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->integer('time_spent')->default(0); // seconds
            $table->timestamp('viewed_at');

            $table->index(['page_type', 'page_id']);
            $table->index('session_id');
            $table->index('viewed_at');
        });

        // User Sessions
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('session_id', 100)->unique();
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->string('device_type', 50)->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('city')->nullable();
            $table->integer('page_views')->default(0);
            $table->integer('duration')->default(0); // seconds
            $table->string('landing_page')->nullable();
            $table->string('exit_page')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();

            $table->index('session_id');
            $table->index('user_id');
            $table->index('started_at');
        });

        // Conversion Tracking
        Schema::create('conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('conversion_type'); // signup, subscription, profile_complete
            $table->string('conversion_value')->nullable();
            $table->decimal('revenue', 10, 2)->nullable();
            $table->string('source')->nullable(); // organic, paid, referral
            $table->string('campaign')->nullable();
            $table->json('attribution_data')->nullable();
            $table->timestamp('converted_at');

            $table->index('conversion_type');
            $table->index('converted_at');
        });

        // A/B Tests
        Schema::create('ab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('variants'); // [{"name": "A", "weight": 50}, {"name": "B", "weight": 50}]
            $table->enum('status', ['draft', 'running', 'completed'])->default('draft');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });

        // A/B Test Assignments
        Schema::create('ab_test_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ab_test_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id', 100)->nullable();
            $table->string('variant_name');
            $table->boolean('converted')->default(false);
            $table->timestamp('assigned_at');
            $table->timestamp('converted_at')->nullable();

            $table->index(['ab_test_id', 'variant_name']);
            $table->unique(['ab_test_id', 'user_id']);
        });

        // Error Logs
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('error_type'); // js_error, api_error, http_error
            $table->string('error_message');
            $table->text('stack_trace')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('line_number')->nullable();
            $table->string('url');
            $table->string('user_agent')->nullable();
            $table->json('context')->nullable();
            $table->integer('count')->default(1);
            $table->timestamp('first_seen_at');
            $table->timestamp('last_seen_at');

            $table->index('error_type');
            $table->index('last_seen_at');
        });

        // Performance Metrics
        Schema::create('performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('metric_type'); // page_load, api_response, db_query
            $table->integer('duration'); // milliseconds
            $table->string('browser')->nullable();
            $table->string('device_type')->nullable();
            $table->json('additional_data')->nullable();
            $table->timestamp('measured_at');

            $table->index('metric_type');
            $table->index('measured_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_metrics');
        Schema::dropIfExists('error_logs');
        Schema::dropIfExists('ab_test_assignments');
        Schema::dropIfExists('ab_tests');
        Schema::dropIfExists('conversions');
        Schema::dropIfExists('user_sessions');
        Schema::dropIfExists('page_views');
        Schema::dropIfExists('analytics_events');
        Schema::dropIfExists('seo_meta');
    }
};
