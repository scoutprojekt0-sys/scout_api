<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_view_stats', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('player_user_id')->nullable();
            $table->string('name')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->dateTime('last_viewed_at')->nullable();
            $table->timestamps();

            $table->unique('player_user_id');
            $table->index('views');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_view_stats');
    }
};

