<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ============================================
        // BİLDİRİM SİSTEMİ
        // ============================================

        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Bildirim Türü
            $table->enum('type', [
                'message',              // Mesaj geldi
                'profile_viewed',       // Profil görüldü
                'interest_shown',       // İlgi gösterildi
                'match_result',         // Maç sonucu
                'league_update',        // Lig güncellemesi
                'coach_offer',          // Antrenör teklifi
                'system_alert',         // Sistem uyarısı
                'achievement',          // Başarı
                'team_invite',          // Takım daveti
                'other'                 // Diğer
            ])->default('other');

            // Bildirim İçeriği
            $table->string('title', 200);
            $table->text('message');
            $table->string('action_url', 500)->nullable();
            $table->foreignId('from_user_id')->nullable()->constrained('users')->nullOnDelete();

            // Meta Bilgisi
            $table->json('metadata')->nullable(); // İlave veri

            // Durum
            $table->boolean('is_read')->default(false);
            $table->dateTime('read_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'is_read']);
                $table->index('created_at');
            });
        }

        // ============================================
        // MESAJLAŞMA SİSTEMİ (Geliştirilmiş)
        // ============================================

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_1_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_2_id')->constrained('users')->cascadeOnDelete();

            // Son Mesaj
            $table->text('last_message')->nullable();
            $table->dateTime('last_message_at')->nullable();

            // Okuma Durumu
            $table->boolean('user_1_read')->default(true);
            $table->boolean('user_2_read')->default(true);

            $table->timestamps();

            $table->index(['user_1_id', 'user_2_id']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();

            // İçerik
            $table->text('content');
            $table->json('attachments')->nullable();

            // Durum
            $table->boolean('is_read')->default(false);
            $table->dateTime('read_at')->nullable();

            // Düzenleme
            $table->text('edited_content')->nullable();
            $table->dateTime('edited_at')->nullable();

            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });

        // ============================================
        // OYUNCU ARAMA (MENAJER İÇİN)
        // ============================================

        if (!Schema::hasTable('player_searches')) {
            Schema::create('player_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manager_id')->constrained('users')->cascadeOnDelete();

            // Arama Kriterleri
            $table->enum('sport', ['football', 'basketball', 'volleyball'])->nullable();
            $table->string('position', 100)->nullable();
            $table->enum('gender', ['male', 'female', 'mixed'])->nullable();
            $table->unsignedSmallInteger('min_age')->nullable();
            $table->unsignedSmallInteger('max_age')->nullable();
            $table->unsignedSmallInteger('min_height')->nullable();
            $table->unsignedSmallInteger('max_height')->nullable();
            $table->json('skill_levels')->nullable(); // Teknik seviye
            $table->json('locations')->nullable(); // Konum

            // İstatistik Kriterleri
            $table->decimal('min_rating', 3, 1)->nullable();
            $table->unsignedSmallInteger('min_goals')->nullable();
            $table->unsignedSmallInteger('min_matches')->nullable();

            // Durum
            $table->boolean('is_active')->default(true);
            $table->boolean('is_saved')->default(false);

            $table->timestamps();

                $table->index(['manager_id', 'sport']);
            });
        }

        if (!Schema::hasTable('player_search_results')) {
            Schema::create('player_search_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('search_id')->constrained('player_searches')->cascadeOnDelete();
            $table->foreignId('player_id')->constrained('users')->cascadeOnDelete();

            // Eşleşme Puanı
            $table->decimal('match_score', 5, 2); // 0-100
            $table->json('match_details')->nullable(); // Neden eşleşti

            $table->timestamps();

                $table->unique(['search_id', 'player_id']);
            });
        }

        // ============================================
        // YARDIM SİSTEMİ (SUPPORT/FAQ)
        // ============================================

        Schema::create('help_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->unsignedSmallInteger('order')->default(0);

            $table->timestamps();
        });

        Schema::create('help_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('help_categories')->cascadeOnDelete();

            $table->string('title', 200);
            $table->string('slug', 200)->unique();
            $table->longText('content');

            // SEO
            $table->text('meta_description')->nullable();
            $table->json('keywords')->nullable();

            // İstatistikler
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('unhelpful_count')->default(0);

            // Durum
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('order')->default(0);

            $table->timestamps();

            $table->index(['category_id', 'is_published']);
        });

        Schema::create('faq', function (Blueprint $table) {
            $table->id();
            $table->string('question', 300);
            $table->longText('answer');

            // Sınıflandırma
            $table->enum('user_type', ['player', 'manager', 'coach', 'scout', 'all'])->default('all');
            $table->enum('topic', [
                'account',
                'profile',
                'messaging',
                'search',
                'contracts',
                'payments',
                'technical',
                'other'
            ])->default('other');

            // İstatistikler
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('helpful_count')->default(0);

            // Durum
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('order')->default(0);

            $table->timestamps();

            $table->index(['user_type', 'topic', 'is_active']);
        });

        // ============================================
        // PROFIL SAYFASI AYARLARI
        // ============================================

        Schema::create('profile_page_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Görünürlük
            $table->boolean('show_contact_button')->default(true);
            $table->boolean('show_message_button')->default(true);
            $table->boolean('show_profile_views')->default(true);
            $table->boolean('show_statistics')->default(true);

            // Profil Özellikleri
            $table->boolean('allow_direct_message')->default(true);
            $table->boolean('allow_profile_view')->default(true);

            // Gizlilik
            $table->boolean('is_profile_public')->default(true);
            $table->boolean('hide_email')->default(true);
            $table->boolean('hide_phone')->default(true);

            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_page_settings');
        Schema::dropIfExists('faq');
        Schema::dropIfExists('help_articles');
        Schema::dropIfExists('help_categories');
        // Shared search tables are defined in other migrations.
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        // Shared notifications table is defined in other migrations.
    }
};
