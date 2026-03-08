<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ulke Bilgileri Tablosu
        if (!Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
                $table->id();
                $table->string('code', 3)->unique(); // TR, EN, DE, FR, ES, IT, PT, NL, BE, etc
                $table->string('name', 100);
                $table->string('currency_code', 3); // TRY, EUR, GBP, USD, etc
                $table->string('currency_symbol', 10);
                $table->decimal('currency_rate', 10, 4)->default(1); // USD'ye karsi

                // Spor Tercihleri
                $table->json('popular_sports')->default('["football"]'); // Bu ulkede populer sporlar
                $table->json('supported_sports')->default('["football", "basketball", "volleyball"]');

                // Dil Bilgileri
                $table->string('default_language', 10);
                $table->json('supported_languages')->default('["tr"]');

                // Hukuk Bilgileri
                $table->string('legal_system', 100)->nullable(); // Turk Hukuku, Common Law, vb
                $table->string('labor_law_type', 100)->nullable();

                // Bolge Bilgileri
                $table->string('timezone', 50);
                $table->string('region', 100); // Europe, Asia, Americas, Africa, Oceania
                $table->json('cities')->nullable();

                // Statu
                $table->boolean('is_active')->default(true);
                $table->boolean('is_verified')->default(false);

                // Kayit Tarihi
                $table->timestamps();

                $table->index('code');
                $table->index('region');
            });
        } else {
            if (!Schema::hasColumn('countries', 'currency_code')) {
                Schema::table('countries', function (Blueprint $table) {
                    $table->string('currency_code', 3)->default('EUR');
                });
            }
            if (!Schema::hasColumn('countries', 'currency_symbol')) {
                Schema::table('countries', function (Blueprint $table) {
                    $table->string('currency_symbol', 10)->default('€');
                });
            }
            if (!Schema::hasColumn('countries', 'currency_rate')) {
                Schema::table('countries', function (Blueprint $table) {
                    $table->decimal('currency_rate', 10, 4)->default(1);
                });
            }
            if (!Schema::hasColumn('countries', 'popular_sports')) {
                Schema::table('countries', function (Blueprint $table) {
                    $table->json('popular_sports')->nullable();
                });
            }
            if (!Schema::hasColumn('countries', 'supported_sports')) {
                Schema::table('countries', function (Blueprint $table) {
                    $table->json('supported_sports')->nullable();
                });
            }
            if (!Schema::hasColumn('countries', 'default_language')) {
                Schema::table('countries', function (Blueprint $table) {
                    $table->string('default_language', 10)->default('tr');
                });
            }
            if (!Schema::hasColumn('countries', 'supported_languages')) {
                Schema::table('countries', function (Blueprint $table) {
                    $table->json('supported_languages')->nullable();
                });
            }
            if (!Schema::hasColumn('countries', 'legal_system')) {
                Schema::table('countries', function (Blueprint $table) {
                    $table->string('legal_system', 100)->nullable();
                });
            }
            if (!Schema::hasColumn('countries', 'labor_law_type')) {
                Schema::table('countries', function (Blueprint $table) {
                    $table->string('labor_law_type', 100)->nullable();
                });
            }
            if (!Schema::hasColumn('countries', 'timezone')) {
                Schema::table('countries', function (Blueprint $table) {
                    $table->string('timezone', 50)->default('UTC');
                });
            }
            if (!Schema::hasColumn('countries', 'region')) {
                Schema::table('countries', function (Blueprint $table) {
                    $table->string('region', 100)->default('Europe');
                });
            }
            if (!Schema::hasColumn('countries', 'cities')) {
                Schema::table('countries', function (Blueprint $table) {
                    $table->json('cities')->nullable();
                });
            }
            if (!Schema::hasColumn('countries', 'is_active')) {
                Schema::table('countries', function (Blueprint $table) {
                    $table->boolean('is_active')->default(true);
                });
            }
            if (!Schema::hasColumn('countries', 'is_verified')) {
                Schema::table('countries', function (Blueprint $table) {
                    $table->boolean('is_verified')->default(false);
                });
            }
        }

        // Dil Dosyaları (Tercümeler)
        Schema::create('language_translations', function (Blueprint $table) {
            $table->id();
            $table->string('language_code', 10); // tr, en, de, fr, es, etc
            $table->string('key', 255); // 'contract.title', 'lawyer.specialization', etc
            $table->text('value'); // Çevrilmiş metin

            // Bölüm
            $table->enum('category', [
                'common',
                'contracts',
                'lawyers',
                'sports',
                'teams',
                'messaging',
                'notifications',
                'errors',
                'validation'
            ])->default('common');

            $table->timestamps();

            $table->unique(['language_code', 'key']);
            $table->index('category');
        });

        // Ülkeye Özel Hukuk Kuralları
        Schema::create('legal_requirements_by_country', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();

            // Gerekli Belgeler
            $table->json('required_documents')->nullable();

            // Sözleşme Şartları
            $table->text('contract_template')->nullable();
            $table->json('mandatory_clauses')->nullable(); // Zorunlu maddeler
            $table->json('forbidden_clauses')->nullable(); // Yasak maddeler

            // Minimum Maaş
            $table->decimal('minimum_salary', 15, 2)->nullable();
            $table->string('salary_period', 50)->default('monthly'); // aylık, haftalık, vb

            // Vergi Bilgileri
            $table->decimal('income_tax_rate', 5, 2)->nullable();
            $table->decimal('social_security_rate', 5, 2)->nullable();

            // Çalışma Saatleri
            $table->unsignedSmallInteger('max_weekly_hours')->default(40);
            $table->unsignedSmallInteger('min_rest_days_per_week')->default(1);

            // İzin Günleri
            $table->unsignedSmallInteger('annual_leave_days')->default(20);
            $table->unsignedSmallInteger('public_holidays')->default(10);

            // Sözleşme Süresi
            $table->enum('min_contract_duration', ['daily', 'weekly', 'monthly', 'yearly'])->default('monthly');
            $table->enum('max_contract_duration', ['1year', '2years', '3years', '5years', 'unlimited'])->default('unlimited');

            // Fesih Şartları
            $table->unsignedSmallInteger('notice_period_days')->default(30);
            $table->decimal('severance_multiplier', 5, 2)->nullable(); // İşten çıkarmada ödenecek kat

            // Çocuk Koruma
            $table->unsignedSmallInteger('min_age_to_play')->default(18);
            $table->boolean('requires_parental_consent')->default(false);

            // Spor Lisansı
            $table->boolean('requires_sport_license')->default(true);
            $table->string('sport_license_issuer', 100)->nullable();

            // Özel Notlar
            $table->text('special_regulations')->nullable();

            $table->timestamps();

            $table->unique('country_id');
        });

        // Ülkeye Özel Spor Kuralları
        Schema::create('sport_rules_by_country', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->enum('sport', ['football', 'basketball', 'volleyball'])->default('football');

            // Lig Bilgileri
            $table->string('top_league_name', 100)->nullable();
            $table->unsignedSmallInteger('num_teams_in_top_league')->nullable();

            // Oyuncu Kuralları
            $table->unsignedSmallInteger('min_age')->default(16);
            $table->unsignedSmallInteger('max_age')->nullable();
            $table->boolean('allows_foreign_players')->default(true);
            $table->unsignedSmallInteger('max_foreign_players')->nullable();

            // Transfer Kuralları
            $table->enum('transfer_window_type', ['none', 'two_windows', 'anytime'])->default('two_windows');
            $table->json('transfer_windows')->nullable(); // Tarihleri

            // Maaş Sınırlı
            $table->boolean('has_salary_cap')->default(false);
            $table->decimal('salary_cap_amount', 15, 2)->nullable();

            // Kat Sayısı Kuralı
            $table->json('foreign_player_restrictions')->nullable();

            $table->timestamps();

            $table->unique(['country_id', 'sport']);
        });

        // Ülkeye Özel Para Birimi Dönüşüm
        Schema::create('currency_exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('from_currency', 3); // EUR
            $table->string('to_currency', 3);   // USD
            $table->decimal('rate', 10, 6);
            $table->dateTime('last_updated');

            $table->timestamps();

            $table->unique(['from_currency', 'to_currency']);
        });

        // Ülkeye Özel Avukat/Danışman
        Schema::create('localized_professionals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawyer_id')->constrained('lawyers')->cascadeOnDelete();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();

            // Lisans Bilgisi (Ülkeye özel)
            $table->string('local_license_number', 100);
            $table->string('local_bar_association', 100)->nullable();

            // Dil Yetkinliği
            $table->json('languages_spoken')->default('["tr"]');

            // Bölge Uzmanlığı
            $table->json('regions_covered')->nullable();
            $table->json('sports_specialized')->nullable();

            // Statü
            $table->boolean('is_verified_locally')->default(false);

            $table->timestamps();

            $table->unique(['lawyer_id', 'country_id']);
        });

        // Kullanıcı Tercih Ayarları
        Schema::create('user_localization_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Ülke
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();

            // Dil Tercihi
            $table->string('language', 10)->default('tr');

            // Para Birimi
            $table->string('currency_code', 3)->default('TRY');

            // Saat Dilimi
            $table->string('timezone', 50)->nullable();

            // Saatlik Format
            $table->enum('time_format', ['12h', '24h'])->default('24h');

            // Tarih Formatı
            $table->enum('date_format', ['DD/MM/YYYY', 'MM/DD/YYYY', 'YYYY-MM-DD'])->default('DD/MM/YYYY');

            // Ölçü Birimleri
            $table->enum('height_unit', ['cm', 'ft'])->default('cm');
            $table->enum('weight_unit', ['kg', 'lbs'])->default('kg');

            $table->timestamps();

            $table->unique('user_id');
        });

        // Ülkeye Özel Likert İçerik
        Schema::create('localized_content', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();

            // İçerik Türü
            $table->enum('content_type', [
                'welcome_message',
                'terms_of_service',
                'privacy_policy',
                'contract_template',
                'faq',
                'help_guide'
            ])->default('help_guide');

            // İçerik
            $table->longText('content');

            // Dil
            $table->string('language_code', 10);

            // Durum
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['country_id', 'content_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('localized_content');
        Schema::dropIfExists('user_localization_settings');
        Schema::dropIfExists('localized_professionals');
        Schema::dropIfExists('currency_exchange_rates');
        Schema::dropIfExists('sport_rules_by_country');
        Schema::dropIfExists('legal_requirements_by_country');
        Schema::dropIfExists('language_translations');
    }
};
