<?php

namespace Database\Seeders;

use App\Models\AmateurTeam;
use App\Models\CommunityEvent;
use App\Models\FreeAgentListing;
use App\Models\PlayerVideoPortfolio;
use App\Models\TrialRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AmateurFootballSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('⚽ Amatör Futbol Verileri Oluşturuluyor...');

        // Test Oyuncuları
        $this->command->info('👤 Amatör Oyuncular...');

        $player1 = User::create([
            'name' => 'Emre Yıldız',
            'email' => 'emre.yildiz@test.com',
            'password' => Hash::make('Password123'),
            'role' => 'player',
            'city' => 'Istanbul',
        ]);

        \App\Models\PlayerProfile::create([
            'user_id' => $player1->id,
            'birth_year' => 1998,
            'date_of_birth' => '1998-05-12',
            'position' => 'Orta Saha',
            'height_cm' => 175,
            'weight_kg' => 70,
            'bio' => 'Amatör futbolcu, 3 yıl mahalle ligi deneyimi',
        ]);

        $player2 = User::create([
            'name' => 'Mehmet Kara',
            'email' => 'mehmet.kara@test.com',
            'password' => Hash::make('Password123'),
            'role' => 'player',
            'city' => 'Ankara',
        ]);

        \App\Models\PlayerProfile::create([
            'user_id' => $player2->id,
            'birth_year' => 2000,
            'date_of_birth' => '2000-08-20',
            'position' => 'Kaleci',
            'height_cm' => 188,
            'weight_kg' => 82,
            'bio' => 'Üniversite takımında kaleci',
        ]);

        // Amatör Takımlar
        $this->command->info('🏟️ Amatör Takımlar...');

        $team1 = AmateurTeam::create([
            'user_id' => $player1->id,
            'team_name' => 'Kadıköy Spor',
            'team_type' => 'neighborhood',
            'city' => 'Istanbul',
            'district' => 'Kadıköy',
            'neighborhood' => 'Moda',
            'description' => 'Kadıköy bölgesinde halı saha takımı. Her hafta düzenli maçlarımız var.',
            'home_field' => 'Moda Halı Saha',
            'field_type' => 'halısaha',
            'practice_days' => 'Salı, Perşembe',
            'practice_time' => '19:00-21:00',
            'current_players' => 15,
            'needed_players' => 3,
            'needed_positions' => ['Forvet', 'Stoper'],
            'monthly_fee' => 200,
            'accepts_new_players' => true,
            'contact_phone' => '0555 123 4567',
        ]);

        $team2 = AmateurTeam::create([
            'user_id' => $player2->id,
            'team_name' => 'Çankaya Gençlik',
            'team_type' => 'club',
            'city' => 'Ankara',
            'district' => 'Çankaya',
            'neighborhood' => 'Kızılay',
            'description' => 'Çankaya bölgesel liginde mücadele eden genç takım.',
            'home_field' => 'Çankaya Stadı',
            'field_type' => 'grass',
            'practice_days' => 'Pazartesi, Çarşamba, Cuma',
            'practice_time' => '18:00-20:00',
            'current_players' => 22,
            'needed_players' => 2,
            'needed_positions' => ['Kanat'],
            'monthly_fee' => 300,
            'accepts_new_players' => true,
            'contact_phone' => '0555 987 6543',
        ]);

        $team3 = AmateurTeam::create([
            'team_name' => 'Beşiktaş United',
            'team_type' => 'friends',
            'city' => 'Istanbul',
            'district' => 'Beşiktaş',
            'neighborhood' => 'Ortaköy',
            'description' => 'Arkadaş grubu halı saha takımı. Hafta sonları maç yapıyoruz.',
            'home_field' => 'Ortaköy Sports',
            'field_type' => 'halısaha',
            'practice_days' => 'Cumartesi',
            'practice_time' => '10:00-12:00',
            'current_players' => 12,
            'needed_players' => 4,
            'needed_positions' => ['Kaleci', 'Defans', 'Orta Saha'],
            'monthly_fee' => 150,
            'accepts_new_players' => true,
            'contact_phone' => '0555 456 7890',
        ]);

        // Serbest Oyuncu İlanları
        $this->command->info('📢 Serbest Oyuncu İlanları...');

        FreeAgentListing::create([
            'player_user_id' => $player1->id,
            'title' => 'Deneyimli Orta Saha Oyuncusu Aranıyor',
            'preferred_positions' => ['Orta Saha', 'Defansif Orta Saha'],
            'city' => 'Istanbul',
            'district' => 'Kadıköy',
            'availability' => 'immediately',
            'available_days' => ['Salı', 'Perşembe', 'Cumartesi'],
            'available_time' => 'Akşam 19:00 sonrası',
            'skill_level' => 'intermediate',
            'max_monthly_fee' => 250,
            'has_equipment' => true,
            'has_transportation' => true,
            'about' => '3 yıl mahalle ligi deneyimi, düzenli antrenman yapıyorum.',
            'experience' => 'Kadıköy Spor - 2022-2024, Moda FK - 2021-2022',
            'status' => 'active',
        ]);

        // Video Portföy
        $this->command->info('📹 Video Portföy...');

        PlayerVideoPortfolio::create([
            'player_user_id' => $player1->id,
            'title' => '2025 Sezon Özeti - Orta Saha Performansı',
            'description' => 'Kadıköy Spor takımında 2025 sezonunda çıktığım maçların özetleri',
            'video_url' => 'https://www.youtube.com/watch?v=example1',
            'video_type' => 'highlights',
            'recorded_date' => '2025-12-01',
            'views' => 45,
            'likes' => 12,
            'is_featured' => true,
            'is_public' => true,
        ]);

        PlayerVideoPortfolio::create([
            'player_user_id' => $player2->id,
            'title' => 'Kaleci Kurtarışları - Öne Çıkan Anlar',
            'description' => 'Üniversite liginde yaptığım önemli kurtarışlar',
            'video_url' => 'https://www.youtube.com/watch?v=example2',
            'video_type' => 'skills',
            'recorded_date' => '2025-10-15',
            'views' => 32,
            'likes' => 8,
            'is_featured' => false,
            'is_public' => true,
        ]);

        // Deneme Talepleri
        $this->command->info('🤝 Deneme Talepleri...');

        TrialRequest::create([
            'player_user_id' => $player2->id,
            'team_id' => $team1->id,
            'request_type' => 'trial_match',
            'message' => 'Merhaba, kaleci pozisyonunda deneme maçına katılmak istiyorum.',
            'preferred_date' => '2026-03-10',
            'preferred_time' => '19:00',
            'status' => 'pending',
        ]);

        // Topluluk Etkinlikleri
        $this->command->info('🎉 Topluluk Etkinlikleri...');

        CommunityEvent::create([
            'organizer_user_id' => $player1->id,
            'title' => 'Kadıköy Halı Saha Turnuvası',
            'description' => '8 takımlık halı saha turnuvası. Herkes katılabilir!',
            'event_type' => 'tournament',
            'city' => 'Istanbul',
            'district' => 'Kadıköy',
            'venue' => 'Moda Halı Saha',
            'event_date' => '2026-03-15 10:00:00',
            'max_participants' => 64, // 8 takım x 8 oyuncu
            'current_participants' => 24,
            'entry_fee' => 50,
            'is_free' => false,
            'skill_level' => 'all_levels',
            'contact_info' => '0555 123 4567',
            'status' => 'registration_open',
        ]);

        CommunityEvent::create([
            'organizer_user_id' => $player2->id,
            'title' => 'Pazar Sabahı Maç - Oyuncu Aranıyor',
            'description' => 'Pazar sabahı dostluk maçı için 10 oyuncu daha arıyoruz.',
            'event_type' => 'pickup_game',
            'city' => 'Ankara',
            'district' => 'Çankaya',
            'venue' => 'Kuğulu Park Sahası',
            'event_date' => '2026-03-09 09:00:00',
            'max_participants' => 20,
            'current_participants' => 10,
            'entry_fee' => 0,
            'is_free' => true,
            'skill_level' => 'all_levels',
            'contact_info' => 'WhatsApp: 0555 987 6543',
            'status' => 'registration_open',
        ]);

        $this->command->info('✅ Amatör futbol verileri başarıyla oluşturuldu!');
        $this->command->info('');
        $this->command->info('📊 Oluşturulan Veriler:');
        $this->command->info('  - Amatör Oyuncular: 2');
        $this->command->info('  - Amatör Takımlar: 3');
        $this->command->info('  - Serbest Oyuncu İlanları: 1');
        $this->command->info('  - Video Portföy: 2');
        $this->command->info('  - Deneme Talepleri: 1');
        $this->command->info('  - Topluluk Etkinlikleri: 2');
        $this->command->info('');
        $this->command->info('🔑 Amatör Test Hesapları:');
        $this->command->info('  Email: emre.yildiz@test.com | Şifre: Password123');
        $this->command->info('  Email: mehmet.kara@test.com | Şifre: Password123');
    }
}
