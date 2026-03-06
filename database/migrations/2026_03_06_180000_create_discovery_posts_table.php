<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discovery_posts', function (Blueprint $table): void {
            $table->id();
            $table->string('author_role', 20); // manager | coach
            $table->string('author_name')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('position', 30)->nullable();
            $table->unsignedSmallInteger('min_height')->nullable();
            $table->string('dominant_side', 20)->nullable();
            $table->unsignedSmallInteger('age_min')->nullable();
            $table->unsignedSmallInteger('age_max')->nullable();
            $table->string('free_only', 20)->nullable();
            $table->unsignedInteger('budget_min')->nullable();
            $table->unsignedInteger('budget_max')->nullable();
            $table->string('city', 80)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['author_role', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discovery_posts');
    }
};

