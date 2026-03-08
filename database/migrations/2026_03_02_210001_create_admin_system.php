<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Admin Audit Log
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();

            // İşlem Detayları
            $table->enum('action', [
                'user_created',
                'user_updated',
                'user_deleted',
                'user_verified',
                'user_banned',
                'content_removed',
                'report_handled',
                'settings_changed',
                'other'
            ])->default('other');

            $table->string('target_type', 100)->nullable(); // User, Post, Comment, vb
            $table->unsignedBigInteger('target_id')->nullable();
            $table->text('description')->nullable();
            $table->json('changes')->nullable(); // Yapılan değişiklikler
            $table->string('ip_address', 45)->nullable();

            $table->timestamps();

            $table->index(['admin_id', 'created_at']);
        });

        // Sistem İstatistikleri
        Schema::create('system_statistics', function (Blueprint $table) {
            $table->id();

            // Kullanıcı Verileri
            $table->unsignedInteger('total_users')->default(0);
            $table->unsignedInteger('total_players')->default(0);
            $table->unsignedInteger('total_managers')->default(0);
            $table->unsignedInteger('total_coaches')->default(0);
            $table->unsignedInteger('total_scouts')->default(0);
            $table->unsignedInteger('active_users_today')->default(0);

            // İçerik Verileri
            $table->unsignedInteger('total_teams')->default(0);
            $table->unsignedInteger('total_matches')->default(0);
            $table->unsignedInteger('total_contracts')->default(0);

            // İşlem Verileri
            $table->unsignedInteger('total_messages')->default(0);
            $table->unsignedInteger('total_notifications')->default(0);
            $table->unsignedInteger('support_tickets')->default(0);
            $table->unsignedInteger('pending_reports')->default(0);

            // Sistem Sağlığı
            $table->decimal('average_response_time', 5, 2)->nullable(); // ms
            $table->decimal('server_load', 5, 2)->nullable(); // %
            $table->unsignedInteger('database_size')->nullable(); // MB

            $table->date('date');
            $table->timestamps();

            $table->unique('date');
        });

        // Destek Talepleri
        if (!Schema::hasTable('support_tickets')) {
            Schema::create('support_tickets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

                $table->string('title', 200);
                $table->text('description');
                $table->enum('category', [
                    'technical',
                    'billing',
                    'account',
                    'content',
                    'other'
                ])->default('other');

                $table->enum('priority', [
                    'low',
                    'medium',
                    'high',
                    'urgent'
                ])->default('medium');

                $table->enum('status', [
                    'open',
                    'in_progress',
                    'waiting_user',
                    'resolved',
                    'closed'
                ])->default('open');

                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                $table->text('resolution_notes')->nullable();
                $table->dateTime('resolved_at')->nullable();

                $table->timestamps();

                $table->index(['status', 'priority']);
            });
        }

        // Kullanıcı Raporları
        Schema::create('user_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reported_user_id')->constrained('users')->cascadeOnDelete();

            $table->enum('reason', [
                'inappropriate_content',
                'harassment',
                'fraud',
                'spam',
                'other'
            ])->default('other');

            $table->text('description');
            $table->json('evidence')->nullable(); // Screenshots, links, vb

            $table->enum('status', [
                'pending',
                'under_review',
                'resolved',
                'dismissed'
            ])->default('pending');

            $table->text('admin_notes')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('handled_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        // İçerik Moderasyonu
        Schema::create('content_moderation', function (Blueprint $table) {
            $table->id();
            $table->string('content_type', 100); // profile_photo, message, comment, vb
            $table->unsignedBigInteger('content_id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->text('content_preview')->nullable();
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'removed'
            ])->default('pending');

            $table->text('rejection_reason')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('moderated_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'content_type']);
        });

        // Sistem Ayarları
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();

            // Site Bilgileri
            $table->string('site_name', 100)->default('Scout Platform');
            $table->string('site_url', 255);
            $table->text('site_description')->nullable();
            $table->string('support_email', 100)->nullable();

            // Email Ayarları
            $table->string('mail_from', 100)->nullable();
            $table->string('mail_host', 100)->nullable();
            $table->unsignedSmallInteger('mail_port')->default(587);

            // Sistem Limitleri
            $table->unsignedSmallInteger('max_upload_size')->default(50); // MB
            $table->unsignedSmallInteger('max_messages_per_hour')->default(100);
            $table->unsignedSmallInteger('user_registration_limit')->default(1000); // per day

            // Gizlilik
            $table->boolean('require_email_verification')->default(true);
            $table->boolean('require_phone_verification')->default(false);
            $table->unsignedSmallInteger('password_min_length')->default(8);

            // Özellikler
            $table->boolean('enable_direct_messaging')->default(true);
            $table->boolean('enable_user_registration')->default(true);
            $table->boolean('enable_social_login')->default(true);

            // Maintenance
            $table->boolean('maintenance_mode')->default(false);
            $table->text('maintenance_message')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('content_moderation');
        Schema::dropIfExists('user_reports');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('system_statistics');
        Schema::dropIfExists('admin_logs');
    }
};
