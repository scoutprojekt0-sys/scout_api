<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('club_needs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 160);
            $table->string('position', 40);
            $table->unsignedTinyInteger('age_min')->nullable();
            $table->unsignedTinyInteger('age_max')->nullable();
            $table->unsignedBigInteger('budget_max_eur')->nullable();
            $table->string('city', 80)->nullable();
            $table->unsignedTinyInteger('urgency')->default(50);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['status', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_needs');
    }
};
