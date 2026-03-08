<?php

namespace Database\Seeders;

use App\Models\AmateurLeague;
use App\Models\AmateurTeam;
use App\Models\CommunityEvent;
use App\Models\GenderPreference;
use App\Models\PlayerVideoPortfolio;
use App\Models\SportsType;
use App\Models\SportSpecificStat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PlayerProfile;

class MultiSportSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('⚽🏀🏐 Multi-Sport Platform Kurulması Başladı...');

        // Spor türlerini oluştur
        $this->command->info('🎯 Spor Türleri Oluşturuluyor...');

        SportsType::create([
            'name' => 'football',
            'display_name' => 'Futbol',
            'description' => '11 oyuncuyla oynanan takım sporu',
        ]);

        SportsType::create([
            'name' => 'basketball',
            'display_name' => 'Basketbol',
            'description' => '5 oyuncuyla oynanan basket sporu',
        ]);

        SportsType::create([
            'name' => 'volleyball',
            'display_name' => 'Voleybol',
            'description' => '6 oyuncuyla oynanan net sporu',
        ]);

        // FUTBOL OYUNCULARI
        $this->command->info('⚽ Futbol Oyuncuları Oluşturuluyor...');

        $footballMale = User::create([
            'name' => 'Ahmet Demir',
            'email' => 'ahmet.futbol@test.com',
            'password' => Hash::make('Password123'),
            'role' => 'player',
            'city' => 'Istanbul',
        ]);

        PlayerProfile::create([
            'user_id' => $footballMale->id,
            'sport' => 'football',
            'gender' => 'male',
            'birth_year' => 1998,
            'position' => 'Forvet',
            'height_cm' => 180,
            'weight_kg' => 75,
            'bio' => 'Futbol oynayan erkek oyuncu',
        ]);

        GenderPreference::create([
            'user_id' => $footballMale->id,
            'preferred_sport' => 'football',
            'preferred_gender_to_play_with' => 'male',
            'comfortable_mixed_team' => true,
        ]);

        SportSpecificStat::create([
            'player_user_id' => $footballMale->id,
            'sport' => 'football',
            'football_goals' => 25,
            'football_assists' => 8,
        ]);

        $footballFemale = User::create([
            'name' => 'Ayşe Kaya',
            'email' => 'ayse.futbol@test.com',
            'password' => Hash::make('Password123'),
            'role' => 'player',
            'city' => 'Ankara',
        ]);

        PlayerProfile::create([
            'user_id' => $footballFemale->id,
            'sport' => 'football',
            'gender' => 'female',
            'birth_year' => 2000,
            'position' => 'Orta Saha',
            'height_cm' => 170,
            'weight_kg' => 62,
            'bio' => 'Futbol oynayan kadın oyuncu',
        ]);

        GenderPreference::create([
            'user_id' => $footballFemale->id,
            'preferred_sport' => 'football',
            'preferred_gender_to_play_with' => 'female',
            'comfortable_mixed_team' => true,
        ]);

        SportSpecificStat::create([
            'player_user_id' => $footballFemale->id,
            'sport' => 'football',
            'football_goals' => 18,
            'football_assists' => 12,
        ]);

        // BASKETBOL OYUNCULARI
        $this->command->info('🏀 Basketbol Oyuncuları Oluşturuluyor...');

        $basketballMale = User::create([
            'name' => 'Mehmet Yıldız',
            'email' => 'mehmet.basketball@test.com',
            'password' => Hash::make('Password123'),
            'role' => 'player',
            'city' => 'Izmir',
        ]);

        PlayerProfile::create([
            'user_id' => $basketballMale->id,
            'sport' => 'basketball',
            'gender' => 'male',
            'birth_year' => 1997,
            'position' => 'Pivot',
            'height_cm' => 205,
            'weight_kg' => 95,
            'bio' => 'Basketbol oynayan erkek oyuncu',
        ]);

        GenderPreference::create([
            'user_id' => $basketballMale->id,
            'preferred_sport' => 'basketball',
            'preferred_gender_to_play_with' => 'mixed',
            'comfortable_mixed_team' => true,
        ]);

        SportSpecificStat::create([
            'player_user_id' => $basketballMale->id,
            'sport' => 'basketball',
            'basketball_points' => 420,
            'basketball_rebounds' => 180,
            'basketball_assists' => 65,
            'basketball_steals' => 45,
        ]);

        $basketballFemale = User::create([
            'name' => 'Zeynep Çetin',
            'email' => 'zeynep.basketball@test.com',
            'password' => Hash::make('Password123'),
            'role' => 'player',
            'city' => 'Bursa',
        ]);

        PlayerProfile::create([
            'user_id' => $basketballFemale->id,
            'sport' => 'basketball',
            'gender' => 'female',
            'birth_year' => 1999,
            'position' => 'Shooting Guard',
            'height_cm' => 180,
            'weight_kg' => 70,
            'bio' => 'Basketbol oynayan kadın oyuncu',
        ]);

        GenderPreference::create([
            'user_id' => $basketballFemale->id,
            'preferred_sport' => 'basketball',
            'preferred_gender_to_play_with' => 'female',
            'comfortable_mixed_team' => true,
        ]);

        SportSpecificStat::create([
            'player_user_id' => $basketballFemale->id,
            'sport' => 'basketball',
            'basketball_points' => 380,
            'basketball_rebounds' => 140,
            'basketball_assists' => 85,
            'basketball_steals' => 52,
        ]);

        // VOLEYBOL OYUNCULARI
        $this->command->info('🏐 Voleybol Oyuncuları Oluşturuluyor...');

        $volleyballMale = User::create([
            'name' => 'Emre Aydın',
            'email' => 'emre.volleyball@test.com',
            'password' => Hash::make('Password123'),
            'role' => 'player',
            'city' => 'Antalya',
        ]);

        PlayerProfile::create([
            'user_id' => $volleyballMale->id,
            'sport' => 'volleyball',
            'gender' => 'male',
            'birth_year' => 1996,
            'position' => 'Pasör',
            'height_cm' => 190,
            'weight_kg' => 85,
            'bio' => 'Voleybol oynayan erkek oyuncu',
        ]);

        GenderPreference::create([
            'user_id' => $volleyballMale->id,
            'preferred_sport' => 'volleyball',
            'preferred_gender_to_play_with' => 'male',
            'comfortable_mixed_team' => false,
        ]);

        SportSpecificStat::create([
            'player_user_id' => $volleyballMale->id,
            'sport' => 'volleyball',
            'volleyball_aces' => 35,
            'volleyball_kills' => 120,
            'volleyball_blocks' => 45,
            'volleyball_digs' => 160,
        ]);

        $volleyballFemale = User::create([
            'name' => 'Seda Şahin',
            'email' => 'seda.volleyball@test.com',
            'password' => Hash::make('Password123'),
            'role' => 'player',
            'city' => 'Mersin',
        ]);

        PlayerProfile::create([
            'user_id' => $volleyballFemale->id,
            'sport' => 'volleyball',
            'gender' => 'female',
            'birth_year' => 2001,
            'position' => 'Smaçör',
            'height_cm' => 182,
            'weight_kg' => 68,
            'bio' => 'Voleybol oynayan kadın oyuncu',
        ]);

        GenderPreference::create([
            'user_id' => $volleyballFemale->id,
            'preferred_sport' => 'volleyball',
            'preferred_gender_to_play_with' => 'female',
            'comfortable_mixed_team' => true,
        ]);

        SportSpecificStat::create([
            'player_user_id' => $volleyballFemale->id,
            'sport' => 'volleyball',
            'volleyball_aces' => 28,
            'volleyball_kills' => 145,
            'volleyball_blocks' => 38,
            'volleyball_digs' => 185,
        ]);

        // FUTBOL TAKILARI
        $this->command->info('⚽ Futbol Takımları Oluşturuluyor...');

        AmateurTeam::create([
            'user_id' => $footballMale->id,
            'team_name' => 'Istanbul FC - Erkek',
            'team_type' => 'club',
            'sport' => 'football',
            'team_gender' => 'male',
            'city' => 'Istanbul',
            'district' => 'Kadıköy',
            'home_field' => 'Moda Sahası',
            'field_type' => 'grass',
            'practice_days' => 'Salı, Perşembe, Cumartesi',
            'practice_time' => '19:00-21:00',
            'current_players' => 18,
            'needed_players' => 2,
            'needed_positions' => ['Forvet', 'Kaleci'],
            'monthly_fee' => 300,
            'accepts_new_players' => true,
            'contact_phone' => '0555 111 1111',
        ]);

        AmateurTeam::create([
            'team_name' => 'Ankara Kadınlar - Futbol',
            'team_type' => 'club',
            'sport' => 'football',
            'team_gender' => 'female',
            'city' => 'Ankara',
            'district' => 'Çankaya',
            'home_field' => 'Kuğulu Sahası',
            'field_type' => 'grass',
            'practice_days' => 'Çarşamba, Cuma, Pazar',
            'practice_time' => '18:00-20:00',
            'current_players' => 14,
            'needed_players' => 4,
            'needed_positions' => ['Forvet', 'Orta Saha'],
            'monthly_fee' => 250,
            'accepts_new_players' => true,
            'contact_phone' => '0555 222 2222',
        ]);

        // BASKETBOL TAKIMI
        $this->command->info('🏀 Basketbol Takımları Oluşturuluyor...');

        AmateurTeam::create([
            'user_id' => $basketballMale->id,
            'team_name' => 'Izmir Baloncesto',
            'team_type' => 'club',
            'sport' => 'basketball',
            'team_gender' => 'mixed',
            'city' => 'Izmir',
            'district' => 'Alsancak',
            'home_field' => 'Alsancak Spor Salonu',
            'field_type' => 'halısaha',
            'practice_days' => 'Pazartesi, Çarşamba, Cuma',
            'practice_time' => '20:00-22:00',
            'current_players' => 12,
            'needed_players' => 3,
            'needed_positions' => ['Pivot', 'Shooting Guard'],
            'monthly_fee' => 350,
            'accepts_new_players' => true,
            'contact_phone' => '0555 333 3333',
        ]);

        // VOLEYBOL TAKIMI
        $this->command->info('🏐 Voleybol Takımları Oluşturuluyor...');

        AmateurTeam::create([
            'user_id' => $volleyballMale->id,
            'team_name' => 'Antalya Voleybol - Erkek',
            'team_type' => 'club',
            'sport' => 'volleyball',
            'team_gender' => 'male',
            'city' => 'Antalya',
            'district' => 'Muratpaşa',
            'home_field' => 'Muratpaşa Spor Salonu',
            'field_type' => 'halısaha',
            'practice_days' => 'Salı, Perşembe',
            'practice_time' => '19:30-21:30',
            'current_players' => 14,
            'needed_players' => 2,
            'needed_positions' => ['Smaçör', 'Pasör'],
            'monthly_fee' => 280,
            'accepts_new_players' => true,
            'contact_phone' => '0555 444 4444',
        ]);

        AmateurTeam::create([
            'team_name' => 'Mersin Bayan Voleybol',
            'team_type' => 'club',
            'sport' => 'volleyball',
            'team_gender' => 'female',
            'city' => 'Mersin',
            'district' => 'Yenişehir',
            'home_field' => 'Yenişehir Salonu',
            'field_type' => 'halısaha',
            'practice_days' => 'Pazartesi, Çarşamba, Cuma',
            'practice_time' => '18:00-20:00',
            'current_players' => 12,
            'needed_players' => 3,
            'needed_positions' => ['Smaçör', 'Libero'],
            'monthly_fee' => 250,
            'accepts_new_players' => true,
            'contact_phone' => '0555 555 5555',
        ]);

        // TOPLULUK ETKİNLİKLERİ
        $this->command->info('🎉 Topluluk Etkinlikleri Oluşturuluyor...');

        CommunityEvent::create([
            'organizer_user_id' => $footballMale->id,
            'title' => 'Istanbul Futbol Turnuvası - Erkek',
            'event_type' => 'tournament',
            'sport' => 'football',
            'event_gender' => 'male',
            'city' => 'Istanbul',
            'district' => 'Kadıköy',
            'venue' => 'Moda Sahası',
            'event_date' => '2026-03-22 10:00:00',
            'max_participants' => 80,
            'current_participants' => 32,
            'entry_fee' => 100,
            'is_free' => false,
            'skill_level' => 'all_levels',
            'status' => 'registration_open',
        ]);

        CommunityEvent::create([
            'organizer_user_id' => $basketballMale->id,
            'title' => 'Izmir Basketbol Ligi - Karma',
            'event_type' => 'tournament',
            'sport' => 'basketball',
            'event_gender' => 'mixed',
            'city' => 'Izmir',
            'district' => 'Alsancak',
            'venue' => 'Alsancak Spor Salonu',
            'event_date' => '2026-04-05 09:00:00',
            'max_participants' => 50,
            'current_participants' => 25,
            'entry_fee' => 150,
            'is_free' => false,
            'skill_level' => 'intermediate',
            'status' => 'registration_open',
        ]);

        CommunityEvent::create([
            'organizer_user_id' => $volleyballFemale->id,
            'title' => 'Mersin Bayan Voleybol Turnuvası',
            'event_type' => 'tournament',
            'sport' => 'volleyball',
            'event_gender' => 'female',
            'city' => 'Mersin',
            'district' => 'Yenişehir',
            'venue' => 'Yenişehir Salonu',
            'event_date' => '2026-03-30 14:00:00',
            'max_participants' => 60,
            'current_participants' => 18,
            'entry_fee' => 80,
            'is_free' => false,
            'skill_level' => 'all_levels',
            'status' => 'registration_open',
        ]);

        $this->command->info('');
        $this->command->info('✅ Multi-Sport Platform Kurulması Tamamlandı!');
        $this->command->info('');
        $this->command->info('📊 Oluşturulan Veriler:');
        $this->command->info('  ⚽ Futbol:');
        $this->command->info('     - 2 Oyuncu (1 Bay, 1 Bayan)');
        $this->command->info('     - 2 Takım (Bay, Bayan)');
        $this->command->info('     - 1 Turnuva');
        $this->command->info('');
        $this->command->info('  🏀 Basketbol:');
        $this->command->info('     - 2 Oyuncu (1 Bay, 1 Bayan)');
        $this->command->info('     - 1 Takım (Karma)');
        $this->command->info('     - 1 Turnuva');
        $this->command->info('');
        $this->command->info('  🏐 Voleybol:');
        $this->command->info('     - 2 Oyuncu (1 Bay, 1 Bayan)');
        $this->command->info('     - 2 Takım (Bay, Bayan)');
        $this->command->info('     - 1 Turnuva');
        $this->command->info('');
        $this->command->info('🔑 Test Hesapları:');
        $this->command->info('  ⚽ Futbol Erkek: ahmet.futbol@test.com | Password123');
        $this->command->info('  ⚽ Futbol Kadın: ayse.futbol@test.com | Password123');
        $this->command->info('  🏀 Basketbol Erkek: mehmet.basketball@test.com | Password123');
        $this->command->info('  🏀 Basketbol Kadın: zeynep.basketball@test.com | Password123');
        $this->command->info('  🏐 Voleybol Erkek: emre.volleyball@test.com | Password123');
        $this->command->info('  🏐 Voleybol Kadın: seda.volleyball@test.com | Password123');
    }
}
