<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Yasal hizmetler
        Schema::create('legal_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawyer_id')->constrained('lawyers')->cascadeOnDelete();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->enum('service_type', ['contract', 'consultation', 'review', 'negotiation'])->default('consultation');
            $table->decimal('base_price', 12, 2)->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->integer('estimated_hours')->nullable();
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('views_count')->default(0);
            $table->integer('bookings_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['lawyer_id', 'is_active']);
            $table->index('service_type');
        });

        // Yasal sözleşmeler
        Schema::create('legal_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawyer_id')->constrained('lawyers')->cascadeOnDelete();
            $table->foreignId('player_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('club_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('contract_type', ['transfer', 'sponsorship', 'endorsement', 'employment', 'image_rights'])->default('transfer');
            $table->decimal('contract_value', 15, 2)->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->text('terms')->nullable();
            $table->text('conditions')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['draft', 'under_review', 'active', 'completed', 'terminated'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['contract_type', 'status']);
        });

        // Yasal danışmanlıklar
        Schema::create('legal_consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawyer_id')->constrained('lawyers')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('consultation_type', ['labor', 'inheritance', 'tax', 'contract', 'general'])->default('general');
            $table->text('topic')->nullable();
            $table->text('summary')->nullable();
            $table->decimal('fee', 10, 2)->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamp('consultation_date')->nullable();
            $table->integer('duration_minutes')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['consultation_type', 'status']);
        });

        // Hizmet talepleri
        Schema::create('legal_service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawyer_id')->constrained('lawyers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('service_type', ['contract', 'consultation', 'review'])->default('consultation');
            $table->text('description');
            $table->decimal('budget', 12, 2)->nullable();
            $table->date('deadline')->nullable();
            $table->enum('status', ['pending', 'accepted', 'in_progress', 'completed', 'rejected'])->default('pending');
            $table->text('lawyer_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['lawyer_id', 'status']);
            $table->index('user_id');
        });

        // Yasal belge şablonları
        Schema::create('legal_document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->enum('category', ['transfer', 'sponsorship', 'labor', 'nda', 'image_rights', 'other'])->default('other');
            $table->longText('template_content');
            $table->decimal('price', 10, 2)->default(0);
            $table->json('variables')->nullable(); // Şablondaki değişkenler
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('category');
        });

        // Yasal incelemeler/Puanlandırma
        Schema::create('legal_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawyer_id')->constrained('lawyers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('rating')->default(5); // 1-5
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            $table->unique(['lawyer_id', 'user_id']);
            $table->index('rating');
        });

        // Başarılı dava örnekleri
        Schema::create('legal_success_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawyer_id')->constrained('lawyers')->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->text('outcome')->nullable();
            $table->year('year');
            $table->enum('case_type', ['transfer', 'employment', 'sponsorship', 'contract', 'other'])->default('other');
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['lawyer_id', 'is_published']);
        });

        // Yasal danışmanlık paketleri
        Schema::create('legal_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawyer_id')->constrained('lawyers')->cascadeOnDelete();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->enum('package_type', ['basic', 'standard', 'premium', 'custom'])->default('standard');
            $table->decimal('price', 12, 2);
            $table->string('currency', 3)->default('EUR');
            $table->json('included_services')->nullable();
            $table->integer('max_consultations')->nullable();
            $table->integer('max_documents')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['lawyer_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_packages');
        Schema::dropIfExists('legal_success_cases');
        Schema::dropIfExists('legal_reviews');
        Schema::dropIfExists('legal_document_templates');
        Schema::dropIfExists('legal_service_requests');
        Schema::dropIfExists('legal_consultations');
        Schema::dropIfExists('legal_contracts');
        Schema::dropIfExists('legal_services');
    }
};
