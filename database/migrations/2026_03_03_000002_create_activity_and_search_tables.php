<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Activity Logs
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('action', 255);
                $table->string('entity_type', 100)->nullable();
                $table->unsignedBigInteger('entity_id')->nullable();
                $table->json('metadata')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 500)->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index('user_id');
                $table->index('action');
                $table->index('created_at');
            });
        }

        // Player Searches
        if (!Schema::hasTable('player_searches')) {
            Schema::create('player_searches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('manager_id')->constrained('users')->cascadeOnDelete();
                $table->enum('sport', ['football', 'basketball', 'volleyball'])->nullable();
                $table->string('position', 100)->nullable();
                $table->enum('gender', ['male', 'female', 'mixed'])->nullable();
                $table->unsignedSmallInteger('min_age')->nullable();
                $table->unsignedSmallInteger('max_age')->nullable();
                $table->unsignedSmallInteger('min_height')->nullable();
                $table->unsignedSmallInteger('max_height')->nullable();
                $table->json('skill_levels')->nullable();
                $table->json('locations')->nullable();
                $table->decimal('min_rating', 3, 1)->nullable();
                $table->unsignedSmallInteger('min_goals')->nullable();
                $table->unsignedSmallInteger('min_matches')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_saved')->default(false);
                $table->timestamps();

                $table->index('manager_id');
                $table->index('is_saved');
                $table->index('is_active');
            });
        }

        // Player Search Results
        if (!Schema::hasTable('player_search_results')) {
            Schema::create('player_search_results', function (Blueprint $table) {
                $table->id();
                $table->foreignId('search_id')->constrained('player_searches')->cascadeOnDelete();
                $table->foreignId('player_id')->constrained('users')->cascadeOnDelete();
                $table->decimal('match_score', 3, 1);
                $table->timestamps();

                $table->index('search_id');
                $table->index('player_id');
            });
        }
    }

    public function down(): void
    {
        // Shared tables are owned by earlier migrations in this codebase.
    }
};
