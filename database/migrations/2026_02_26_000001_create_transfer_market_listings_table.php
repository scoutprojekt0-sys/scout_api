<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transfer_market_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('asking_fee_eur')->nullable();
            $table->unsignedInteger('salary_min_eur')->nullable();
            $table->unsignedInteger('salary_max_eur')->nullable();
            $table->date('contract_until')->nullable();
            $table->unsignedTinyInteger('form_score')->nullable();
            $table->unsignedSmallInteger('minutes_5_matches')->nullable();
            $table->enum('injury_status', ['fit', 'doubtful', 'injured'])->default('fit');
            $table->enum('market_status', ['open', 'in_talk', 'closed'])->default('open');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['market_status', 'asking_fee_eur']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_market_listings');
    }
};
