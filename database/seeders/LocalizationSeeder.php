<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\LanguageTranslation;
use App\Models\LegalRequirementsByCountry;
use App\Models\SportRulesByCountry;
use Illuminate\Database\Seeder;

class LocalizationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌍 Kültürleştirme Verileri Yükleniyor...');

        // TÜRKİYE
        $turkey = Country::create([
            'code' => 'TR',
            'name' => 'Türkiye',
            'currency_code' => 'TRY',
            'currency_symbol' => '₺',
            'currency_rate' => 1,
            'popular_sports' => ['football', 'basketball', 'volleyball'],
            'supported_sports' => ['football', 'basketball', 'volleyball'],
            'default_language' => 'tr',
            'supported_languages' => ['tr', 'en'],
            'legal_system' => 'Türk Hukuku',
            'labor_law_type' => 'Türk Işçi Hukuku',
            'timezone' => 'Europe/Istanbul',
            'region' => 'Europe/Asia',
            'cities' => ['Istanbul', 'Ankara', 'Izmir', 'Bursa', 'Antalya'],
            'is_active' => true,
            'is_verified' => true,
        ]);

        // ALMANYA
        $germany = Country::create([
            'code' => 'DE',
            'name' => 'Almanya',
            'currency_code' => 'EUR',
            'currency_symbol' => '€',
            'currency_rate' => 36.50,
            'popular_sports' => ['football', 'basketball', 'volleyball'],
            'supported_sports' => ['football', 'basketball', 'volleyball'],
            'default_language' => 'de',
            'supported_languages' => ['de', 'en'],
            'legal_system' => 'Alman Hukuku',
            'labor_law_type' => 'Alman İşçi Hukuku',
            'timezone' => 'Europe/Berlin',
            'region' => 'Europe',
            'cities' => ['Berlin', 'Munich', 'Hamburg', 'Cologne', 'Frankfurt'],
            'is_active' => true,
            'is_verified' => true,
        ]);

        // İNGİLTERE
        $uk = Country::create([
            'code' => 'UK',
            'name' => 'İngiltere',
            'currency_code' => 'GBP',
            'currency_symbol' => '£',
            'currency_rate' => 42.00,
            'popular_sports' => ['football'],
            'supported_sports' => ['football', 'basketball', 'volleyball'],
            'default_language' => 'en',
            'supported_languages' => ['en'],
            'legal_system' => 'Common Law',
            'labor_law_type' => 'Employment Rights Act',
            'timezone' => 'Europe/London',
            'region' => 'Europe',
            'cities' => ['London', 'Manchester', 'Liverpool', 'Arsenal'],
            'is_active' => true,
            'is_verified' => true,
        ]);

        // İSPANYA
        $spain = Country::create([
            'code' => 'ES',
            'name' => 'İspanya',
            'currency_code' => 'EUR',
            'currency_symbol' => '€',
            'currency_rate' => 36.50,
            'popular_sports' => ['football'],
            'supported_sports' => ['football', 'basketball', 'volleyball'],
            'default_language' => 'es',
            'supported_languages' => ['es', 'en'],
            'legal_system' => 'İspanyol Hukuku',
            'labor_law_type' => 'Estatuto de los Trabajadores',
            'timezone' => 'Europe/Madrid',
            'region' => 'Europe',
            'cities' => ['Madrid', 'Barcelona', 'Valencia', 'Seville'],
            'is_active' => true,
            'is_verified' => true,
        ]);

        // ABD
        $usa = Country::create([
            'code' => 'US',
            'name' => 'Amerika Birleşik Devletleri',
            'currency_code' => 'USD',
            'currency_symbol' => '$',
            'currency_rate' => 33.50,
            'popular_sports' => ['basketball', 'football'],
            'supported_sports' => ['football', 'basketball', 'volleyball'],
            'default_language' => 'en',
            'supported_languages' => ['en'],
            'legal_system' => 'Common Law',
            'labor_law_type' => 'Fair Labor Standards Act',
            'timezone' => 'America/New_York',
            'region' => 'Americas',
            'cities' => ['New York', 'Los Angeles', 'Chicago', 'Houston'],
            'is_active' => true,
            'is_verified' => true,
        ]);

        // FRANSA
        $france = Country::create([
            'code' => 'FR',
            'name' => 'Fransa',
            'currency_code' => 'EUR',
            'currency_symbol' => '€',
            'currency_rate' => 36.50,
            'popular_sports' => ['football', 'rugby'],
            'supported_sports' => ['football', 'basketball', 'volleyball'],
            'default_language' => 'fr',
            'supported_languages' => ['fr', 'en'],
            'legal_system' => 'Fransız Hukuku',
            'labor_law_type' => 'Code du Travail',
            'timezone' => 'Europe/Paris',
            'region' => 'Europe',
            'cities' => ['Paris', 'Marseille', 'Lyon', 'Toulouse'],
            'is_active' => true,
            'is_verified' => true,
        ]);

        // TÜRKİYE HUKUK KURALLARI
        LegalRequirementsByCountry::create([
            'country_id' => $turkey->id,
            'required_documents' => ['Kimlik', 'Nüfus Cüzdanı', 'Spor Lisansı'],
            'mandatory_clauses' => ['Maaş', 'Sürü', 'Sorumluluklar'],
            'forbidden_clauses' => ['Çocuk İşçiliği'],
            'minimum_salary' => 3000,
            'salary_period' => 'monthly',
            'income_tax_rate' => 15,
            'social_security_rate' => 19.5,
            'max_weekly_hours' => 45,
            'annual_leave_days' => 20,
            'notice_period_days' => 30,
            'min_age_to_play' => 16,
            'requires_parental_consent' => true,
            'requires_sport_license' => true,
        ]);

        // ALMANYA HUKUK KURALLARI
        LegalRequirementsByCountry::create([
            'country_id' => $germany->id,
            'required_documents' => ['Reisepass', 'Visum'],
            'mandatory_clauses' => ['Salary', 'Duration', 'Duties'],
            'forbidden_clauses' => ['Child Labor'],
            'minimum_salary' => 1500,
            'salary_period' => 'monthly',
            'income_tax_rate' => 20,
            'social_security_rate' => 21,
            'max_weekly_hours' => 40,
            'annual_leave_days' => 25,
            'notice_period_days' => 28,
            'min_age_to_play' => 16,
            'requires_parental_consent' => false,
            'requires_sport_license' => true,
        ]);

        // TÜRKİYE FUTBOL KURALLARI
        SportRulesByCountry::create([
            'country_id' => $turkey->id,
            'sport' => 'football',
            'top_league_name' => 'Süper Lig',
            'num_teams_in_top_league' => 20,
            'min_age' => 16,
            'allows_foreign_players' => true,
            'max_foreign_players' => 7,
            'transfer_window_type' => 'two_windows',
            'transfer_windows' => [
                ['start' => '2026-06-01', 'end' => '2026-08-31'],
                ['start' => '2027-01-01', 'end' => '2027-01-31'],
            ],
            'has_salary_cap' => false,
        ]);

        // ALMANYA FUTBOL KURALLARI
        SportRulesByCountry::create([
            'country_id' => $germany->id,
            'sport' => 'football',
            'top_league_name' => 'Bundesliga',
            'num_teams_in_top_league' => 18,
            'min_age' => 16,
            'allows_foreign_players' => true,
            'max_foreign_players' => 5,
            'transfer_window_type' => 'two_windows',
            'has_salary_cap' => true,
            'salary_cap_amount' => 100000,
        ]);

        $this->command->info('✅ 🌍 Kültürleştirme Verileri Yüklendi!');
    }
}
