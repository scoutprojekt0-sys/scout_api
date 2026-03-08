<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // News Tablosu
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->longText('content')->nullable();
            $table->string('source', 100)->nullable();
            $table->string('image_url', 500)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index('published_at');
            $table->index('is_published');
        });

        // Live Matches Tablosu
        Schema::create('live_matches', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('home_team', 120);
            $table->string('away_team', 120);
            $table->timestamp('match_date');
            $table->unsignedSmallInteger('home_score')->nullable();
            $table->unsignedSmallInteger('away_score')->nullable();
            $table->boolean('is_live')->default(false);
            $table->boolean('is_finished')->default(false);
            $table->string('league', 120)->nullable();
            $table->string('round', 50)->nullable();
            $table->timestamps();

            $table->index('match_date');
            $table->index('is_live');
            $table->index('is_finished');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_matches');
        Schema::dropIfExists('news');
    }
};
