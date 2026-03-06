<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boost_profiles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('position', 40)->nullable();
            $table->string('city', 80)->nullable();
            $table->text('summary')->nullable();
            $table->string('package_code', 20)->nullable();
            $table->string('package_label', 40)->nullable();
            $table->unsignedInteger('price_tl')->default(0);
            $table->boolean('paid')->default(false);
            $table->dateTime('expires_at')->nullable();
            $table->string('card_last4', 4)->nullable();
            $table->timestamps();

            $table->index(['created_at', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boost_profiles');
    }
};

