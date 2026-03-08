<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'city']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'is_read']);
        });

        Schema::table('opportunities', function (Blueprint $table) {
            $table->index(['status', 'city']);
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->index(['opportunity_id', 'status']);
            $table->index(['player_user_id', 'status']);
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->index(['to_user_id', 'status']);
            $table->index(['from_user_id', 'status']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['user_id', 'status']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex(['to_user_id', 'status']);
            $table->dropIndex(['from_user_id', 'status']);
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex(['opportunity_id', 'status']);
            $table->dropIndex(['player_user_id', 'status']);
        });

        Schema::table('opportunities', function (Blueprint $table) {
            $table->dropIndex(['status', 'city']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_read']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'city']);
        });
    }
};
