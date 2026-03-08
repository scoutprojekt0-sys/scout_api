<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Takım maçları
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->string('home_team', 120);
            $table->string('away_team', 120);
            $table->unsignedTinyInteger('home_score')->nullable();
            $table->unsignedTinyInteger('away_score')->nullable();
            $table->string('competition', 100)->nullable(); // Liga, Kupa
            $table->string('venue', 120)->nullable(); // Stadyum
            $table->dateTime('match_date');
            $table->enum('status', ['scheduled', 'live', 'finished', 'postponed', 'cancelled'])->default('scheduled');
            $table->timestamps();

            $table->index(['match_date', 'status']);
        });

        // Takım kadro (squad)
        Schema::create('team_squads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('jersey_number', 3)->nullable();
            $table->string('position', 40)->nullable();
            $table->enum('status', ['active', 'injured', 'suspended', 'released'])->default('active');
            $table->date('joined_date');
            $table->date('left_date')->nullable();
            $table->timestamps();

            $table->unique(['team_user_id', 'player_user_id', 'joined_date']);
        });

        // Abonelik/Premium paketler
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2); // Aylık fiyat
            $table->unsignedSmallInteger('duration_days')->default(30);
            $table->json('features')->nullable(); // ["unlimited_contacts", "advanced_stats"]
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained('subscription_plans')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->decimal('amount_paid', 8, 2);
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // Destek talepleri (support tickets)
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('subject', 160);
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->enum('category', ['technical', 'account', 'billing', 'general'])->default('general');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('support_ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->boolean('is_staff_reply')->default(false);
            $table->timestamps();

            $table->index(['ticket_id', 'created_at']);
        });

        // Bildirim ayarları
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->cascadeOnDelete();
            $table->boolean('email_new_message')->default(true);
            $table->boolean('email_new_application')->default(true);
            $table->boolean('email_application_status')->default(true);
            $table->boolean('email_new_opportunity')->default(false);
            $table->boolean('email_profile_view')->default(false);
            $table->boolean('push_new_message')->default(true);
            $table->boolean('push_new_application')->default(true);
            $table->boolean('push_application_status')->default(true);
            $table->boolean('sms_important_updates')->default(false);
            $table->timestamps();
        });

        // Şikayet/Raporlama sistemi
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reported_user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('reported_entity_type', 50)->nullable(); // "Media", "Contact", etc.
            $table->unsignedBigInteger('reported_entity_id')->nullable();
            $table->enum('reason', ['spam', 'inappropriate', 'fake_profile', 'harassment', 'other'])->default('other');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'action_taken', 'dismissed'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('notification_settings');
        Schema::dropIfExists('support_ticket_messages');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('user_subscriptions');
        Schema::dropIfExists('subscription_plans');
        Schema::dropIfExists('team_squads');
        Schema::dropIfExists('matches');
    }
};
