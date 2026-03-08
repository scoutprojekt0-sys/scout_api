<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Some earlier migrations in this project already create legal core tables.
        // Guard to avoid duplicate-table crashes during fresh migrations.
        if (Schema::hasTable('contracts')) {
            return;
        }

        // Avukatlar/Hukuk Danışmanları Tablosu
        if (!Schema::hasTable('lawyers')) {
            Schema::create('lawyers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Avukat Bilgileri
            $table->string('license_number', 100)->unique(); // Avukat lisansı
            $table->string('specialization', 100); // Uzmanlaşma (Sports Law, Contract, vb)
            $table->text('bio')->nullable();

            // Ofis Bilgileri
            $table->string('office_name', 150)->nullable();
            $table->string('office_address', 255)->nullable();
            $table->string('office_phone', 30)->nullable();
            $table->string('office_email', 120)->nullable();

            // Deneyim
            $table->unsignedSmallInteger('years_experience')->nullable();
            $table->json('past_clients')->nullable(); // Geçmiş danışanlar sayısı

            // Ücret Bilgileri
            $table->decimal('hourly_rate', 10, 2)->nullable(); // Saatlik ücret
            $table->decimal('contract_fee', 10, 2)->nullable(); // Sözleşme ücreti

            // Durum
            $table->boolean('is_verified')->default(false); // Doğrulanmış avukat
            $table->boolean('is_active')->default(true);
            $table->enum('license_status', ['valid', 'expired', 'suspended'])->default('valid');

                $table->timestamps();

                $table->index('is_verified');
            });
        }

        // Sözleşmeler Tablosu
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('manager_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lawyer_id')->nullable()->constrained('lawyers')->nullOnDelete();

            // Sözleşme Bilgisi
            $table->string('contract_number', 100)->unique();
            $table->enum('type', [
                'player_team',
                'transfer_agreement',
                'endorsement',
                'image_rights',
                'commercial',
                'other'
            ])->default('player_team');

            // Tarihler
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('contract_date');

            // Para Bilgileri
            $table->decimal('total_amount', 15, 2)->nullable(); // Toplam tutar
            $table->json('payment_schedule')->nullable(); // Ödeme planı

            // Şartlar
            $table->text('terms_conditions');
            $table->json('clauses')->nullable(); // Maddeler
            $table->json('special_conditions')->nullable(); // Özel şartlar

            // Durum
            $table->enum('status', [
                'draft',
                'proposed',
                'under_negotiation',
                'awaiting_signature',
                'signed',
                'active',
                'completed',
                'terminated',
                'disputed'
            ])->default('draft');

            // İmzalar
            $table->dateTime('player_signed_at')->nullable();
            $table->dateTime('manager_signed_at')->nullable();
            $table->dateTime('lawyer_approved_at')->nullable();

            // Dosyalar
            $table->json('documents')->nullable(); // PDF, Word vb dosyalar
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['player_user_id', 'status']);
            $table->index(['manager_user_id', 'status']);
        });

        // Sözleşme Müzakeresi Tablosu
        Schema::create('contract_negotiations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete();
            $table->foreignId('lawyer_id')->constrained('lawyers')->cascadeOnDelete();

            // Müzakere Aşaması
            $table->enum('stage', [
                'initial_review',
                'term_discussion',
                'clause_review',
                'amendment',
                'final_review',
                'signature_ready'
            ])->default('initial_review');

            // Talep ve Cevap
            $table->text('player_request')->nullable(); // Futbolcunun talepleri
            $table->text('manager_offer')->nullable(); // Menajerin teklifi
            $table->text('lawyer_recommendation')->nullable(); // Avukatın önerisi

            // Gözlemler
            $table->json('disputed_clauses')->nullable(); // Anlaşılmayan maddeler
            $table->json('amendments')->nullable(); // Değişiklik talepleri

            // Tarih ve Durum
            $table->dateTime('proposed_at');
            $table->dateTime('reviewed_at')->nullable();
            $table->enum('result', ['accepted', 'rejected', 'pending', 'revised'])->default('pending');

            $table->timestamps();

            $table->index(['contract_id', 'stage']);
        });

        // Sözleşme Versiyonları (History)
        Schema::create('contract_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete();

            $table->unsignedSmallInteger('version_number');
            $table->text('changes_description')->nullable(); // Ne değişti
            $table->json('content')->nullable(); // Sözleşme içeriği
            $table->enum('modified_by', ['player', 'manager', 'lawyer'])->default('lawyer');

            $table->dateTime('created_at');

            $table->index(['contract_id', 'version_number']);
        });

        // İmza Talepleri Tablosu
        Schema::create('signature_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete();

            // Kime
            $table->enum('requested_from', ['player', 'manager'])->default('player');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Avukat Tarafından
            $table->foreignId('lawyer_id')->constrained('lawyers')->cascadeOnDelete();

            // Talep Bilgisi
            $table->text('request_message')->nullable();
            $table->dateTime('requested_at');
            $table->dateTime('deadline')->nullable();

            // İmza Bilgisi
            $table->enum('status', ['pending', 'signed', 'rejected', 'expired'])->default('pending');
            $table->dateTime('signed_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // İmza Detayları
            $table->string('signature_ip', 50)->nullable();
            $table->string('signature_device', 100)->nullable();

            $table->timestamps();

            $table->index(['contract_id', 'status']);
            $table->index(['user_id', 'status']);
        });

        // Sözleşme İtiraçları/Uyuşmazlıklar
        Schema::create('contract_disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete();

            // İtiraç Sahibi
            $table->enum('raised_by', ['player', 'manager', 'lawyer'])->default('player');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // İtiraç Bilgisi
            $table->string('title', 200);
            $table->text('description');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');

            // Hangi madde
            $table->json('related_clauses')->nullable();

            // Durum
            $table->enum('status', [
                'reported',
                'under_review',
                'mediation',
                'resolved',
                'escalated'
            ])->default('reported');

            // Çözüm
            $table->text('resolution')->nullable();
            $table->dateTime('resolved_at')->nullable();

            $table->timestamps();

            $table->index(['contract_id', 'status']);
        });

        // Avukat Tarafından Yapılan İncelemeler
        Schema::create('lawyer_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete();
            $table->foreignId('lawyer_id')->constrained('lawyers')->cascadeOnDelete();

            // İnceleme Bilgisi
            $table->text('legal_review');
            $table->json('risk_assessment')->nullable(); // Risk değerlendirmesi
            $table->json('recommendations')->nullable(); // Öneriler
            $table->integer('compliance_score')->nullable(); // 1-100

            // Sakıncalar
            $table->json('concerns')->nullable();
            $table->json('required_changes')->nullable();

            // Durum
            $table->enum('review_status', ['pending', 'approved', 'needs_revision', 'rejected'])->default('pending');

            $table->timestamps();

            $table->index(['contract_id', 'review_status']);
        });

        // Sözleşme Şablonları
        Schema::create('contract_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->enum('type', ['player_team', 'transfer', 'endorsement', 'commercial'])->default('player_team');
            $table->text('description')->nullable();

            // İçerik
            $table->longText('template_content');
            $table->json('variables')->nullable(); // {player_name}, {salary}, vb
            $table->json('default_clauses')->nullable();

            // Durum
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);

            $table->timestamps();
        });

        // Sözleşme Tarihi Tablosu
        Schema::create('contract_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete();

            // İşlem
            $table->enum('action', [
                'created',
                'proposed',
                'reviewed',
                'negotiated',
                'amended',
                'signed',
                'activated',
                'terminated',
                'disputed'
            ])->default('created');

            // Kim yaptı
            $table->enum('performed_by_role', ['player', 'manager', 'lawyer', 'admin']);
            $table->foreignId('performed_by_user_id')->constrained('users')->cascadeOnDelete();

            // Detay
            $table->text('details')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['contract_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_history');
        Schema::dropIfExists('contract_templates');
        Schema::dropIfExists('lawyer_reviews');
        Schema::dropIfExists('contract_disputes');
        Schema::dropIfExists('signature_requests');
        Schema::dropIfExists('contract_versions');
        Schema::dropIfExists('contract_negotiations');
        // Shared legal core tables may be owned by earlier migrations.
    }
};
