<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Gelişmiş Mesajlaşma Sistemi
        Schema::create('player_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('to_user_id')->constrained('users')->cascadeOnDelete();

            // Mesaj İçeriği
            $table->string('subject', 200)->nullable();
            $table->text('message');
            $table->enum('type', ['direct_message', 'inquiry', 'offer', 'feedback'])->default('direct_message');

            // Okunma Bilgisi
            $table->boolean('is_read')->default(false);
            $table->dateTime('read_at')->nullable();

            // Anonim Özelliği
            $table->boolean('is_anonymous')->default(false);
            $table->string('anonymous_name', 100)->nullable(); // "Gizli Menajeri" gibi

            // Dosya Ekleri
            $table->json('attachments')->nullable();

            // Arşiv
            $table->boolean('archived_by_sender')->default(false);
            $table->boolean('archived_by_recipient')->default(false);

            $table->timestamps();

            $table->index(['to_user_id', 'is_read']);
            $table->index(['from_user_id', 'created_at']);
        });

        // Menajerin Bakış Bildirimler (Anonim)
        Schema::create('manager_scout_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('manager_scout_id')->constrained('users')->cascadeOnDelete();

            // Bakış Bilgisi
            $table->dateTime('view_time');
            $table->enum('view_type', ['profile_view', 'video_view', 'stats_view', 'full_profile'])->default('profile_view');
            $table->unsignedSmallInteger('duration_seconds')->default(0); // Ne kadar baktı

            // Anonim Koruma
            $table->boolean('is_anonymous')->default(true);
            $table->string('viewer_display_name', 100)->nullable(); // "Scout'tan" gibi mesajlar

            // Bildirim Gönderildi Mi
            $table->boolean('notification_sent')->default(false);
            $table->dateTime('notification_sent_at')->nullable();

            $table->timestamps();

            $table->index(['player_user_id', 'view_time']);
        });

        // Anonim Bildirimler
        Schema::create('anonymous_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('triggered_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            // Bildirim Bilgisi
            $table->enum('notification_type', [
                'anonymous_profile_view',
                'anonymous_message',
                'scout_interest',
                'manager_interest',
                'mystery_view'
            ])->default('anonymous_profile_view');

            $table->text('message');
            $table->string('emoji', 50)->nullable(); // 👀, 💌, ⭐ vb

            // Detaylar
            $table->json('metadata')->nullable(); // View süresi, hangi bölüm vb

            // Okunma Durumu
            $table->boolean('is_read')->default(false);
            $table->dateTime('read_at')->nullable();

            // Kirlenme (Hint)
            $table->string('hint', 100)->nullable(); // "Büyük bir takımdan", "Avrupa'dan" vb

            $table->timestamps();

            $table->index(['player_user_id', 'is_read']);
        });

        // Oyuncu Sohbet Odaları
        Schema::create('player_chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->json('participant_ids'); // [user_id1, user_id2, ...]
            $table->string('room_name', 200)->nullable();
            $table->enum('type', ['direct', 'group', 'team'])->default('direct');

            // Son Mesaj
            $table->text('last_message')->nullable();
            $table->dateTime('last_message_time')->nullable();

            // Durum
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['created_at']);
        });

        // Chat Mesajları
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('player_chat_rooms')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();

            // Mesaj
            $table->text('message');
            $table->json('attachments')->nullable();

            // Emoji Reaksiyonları
            $table->json('reactions')->nullable(); // {user_id: emoji}

            // Silme Bilgisi
            $table->boolean('is_deleted')->default(false);
            $table->dateTime('deleted_at')->nullable();

            // Düzenleme
            $table->boolean('is_edited')->default(false);
            $table->dateTime('edited_at')->nullable();

            $table->timestamps();

            $table->index(['room_id', 'created_at']);
        });

        // Chat Okunma Durumu
        Schema::create('chat_message_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('chat_messages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->dateTime('read_at');

            $table->timestamps();

            $table->unique(['message_id', 'user_id']);
        });

        // Oyuncu İlgi Bildirleri (Menajerin Gizli İlgisi)
        Schema::create('secret_interest_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();

            // Bildirim
            $table->string('title', 200);
            $table->text('message');
            $table->enum('icon', ['👀', '💌', '⭐', '🎯', '🚀', '❓'])->default('👀');

            // Hint Bilgileri
            $table->string('hint_location', 100)->nullable(); // "Avrupa'dan", "Türkiye'nin başkentinden"
            $table->string('hint_level', 100)->nullable(); // "Profesyonel", "Yarı-profesyonel"
            $table->string('hint_position', 100)->nullable(); // "Senin pozisyonunda ilgi"

            // Okunma
            $table->boolean('is_read')->default(false);
            $table->dateTime('read_at')->nullable();

            // Mystery (Merak Uyandırıcı)
            $table->boolean('is_mystery')->default(true);
            $table->unsignedSmallInteger('mystery_level')->default(1); // 1-5 (1=çok gizli, 5=açık)

            $table->timestamps();

            $table->index(['player_user_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('secret_interest_notifications');
        Schema::dropIfExists('chat_message_reads');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('player_chat_rooms');
        Schema::dropIfExists('anonymous_notifications');
        Schema::dropIfExists('manager_scout_views');
        Schema::dropIfExists('player_messages');
    }
};
