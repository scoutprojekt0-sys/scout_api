<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Contact;
use App\Models\Favorite;
use App\Models\LiveMatch;
use App\Models\Media;
use App\Models\Notification;
use App\Models\Opportunity;
use App\Models\PlayerProfile;
use App\Models\StaffProfile;
use App\Models\TeamProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Test kullanıcıları oluştur
        $player = User::query()->firstOrCreate(
            ['email' => 'oyuncu@test.com'],
            [
                'name' => 'Ahmet Yılmaz',
                'password' => Hash::make('Password123'),
                'role' => 'player',
                'city' => 'Istanbul',
                'phone' => '+905551234567',
            ]
        );

        PlayerProfile::query()->firstOrCreate(
            ['user_id' => $player->id],
            [
                'birth_year' => 2000,
                'position' => 'Forvet',
                'dominant_foot' => 'Sağ',
                'height_cm' => 180,
                'weight_kg' => 75,
                'bio' => 'Profesyonel futbolcu, 5 yıl deneyim',
                'current_team' => 'İstanbul Spor',
            ]
        );

        $team = User::query()->firstOrCreate(
            ['email' => 'takim@test.com'],
            [
                'name' => 'Ankara Futbol Kulübü',
                'password' => Hash::make('Password123'),
                'role' => 'team',
                'city' => 'Ankara',
                'phone' => '+905559876543',
            ]
        );

        TeamProfile::query()->firstOrCreate(
            ['user_id' => $team->id],
            [
                'team_name' => 'Ankara FK',
                'league_level' => 'Bölgesel Amatör Lig',
                'city' => 'Ankara',
                'founded_year' => 1980,
                'needs_text' => 'Genç forvet oyuncuları arıyoruz',
            ]
        );

        $scout = User::query()->firstOrCreate(
            ['email' => 'scout@test.com'],
            [
                'name' => 'Mehmet Kaya',
                'password' => Hash::make('Password123'),
                'role' => 'scout',
                'city' => 'Izmir',
                'phone' => '+905557894561',
            ]
        );

        StaffProfile::query()->firstOrCreate(
            ['user_id' => $scout->id],
            [
                'role_type' => 'scout',
                'organization' => 'Profesyonel Scout',
                'experience_years' => 10,
                'bio' => '10 yıllık scout deneyimi',
            ]
        );

        $manager = User::query()->firstOrCreate(
            ['email' => 'menejer@test.com'],
            [
                'name' => 'Ali Demir',
                'password' => Hash::make('Password123'),
                'role' => 'manager',
                'city' => 'Istanbul',
                'phone' => '+905556547893',
            ]
        );

        StaffProfile::query()->firstOrCreate(
            ['user_id' => $manager->id],
            [
                'role_type' => 'manager',
                'organization' => 'Star Management',
                'experience_years' => 8,
                'bio' => 'Profesyonel oyuncu menajeri',
            ]
        );

        // Fırsatlar oluştur
        $opportunity = Opportunity::query()->firstOrCreate(
            [
                'team_user_id' => $team->id,
                'title' => 'Genç Forvet Aranıyor',
            ],
            [
                'position' => 'Forvet',
                'age_min' => 18,
                'age_max' => 25,
                'city' => 'Ankara',
                'details' => 'Bölgesel Amatör Lig için genç ve yetenekli forvet oyuncuları arıyoruz.',
                'status' => 'open',
            ]
        );

        // Başvuru oluştur
        Application::query()->firstOrCreate(
            [
                'opportunity_id' => $opportunity->id,
                'player_user_id' => $player->id,
            ],
            [
                'message' => 'Merhaba, bu pozisyon için başvurmak istiyorum.',
                'status' => 'pending',
            ]
        );

        // Medya ekle
        Media::query()->firstOrCreate(
            [
                'user_id' => $player->id,
                'url' => 'https://example.com/videos/highlights.mp4',
            ],
            [
                'type' => 'video',
                'thumb_url' => 'https://example.com/thumbnails/highlights.jpg',
                'title' => 'Sezon Performans Özeti',
            ]
        );

        // Favori ekle
        Favorite::query()->firstOrCreate([
            'user_id' => $scout->id,
            'target_user_id' => $player->id,
        ]);

        // Bildirim ekle
        Notification::query()->firstOrCreate(
            [
                'user_id' => $player->id,
                'type' => 'new_message',
            ],
            [
                'payload' => ['message' => 'Yeni mesajınız var'],
                'is_read' => false,
            ]
        );

        // İletişim/Mesaj ekle
        Contact::query()->firstOrCreate(
            [
                'from_user_id' => $scout->id,
                'to_user_id' => $player->id,
            ],
            [
                'subject' => 'Performansınız hakkında',
                'message' => 'Merhaba, performansınızı çok beğendim. Görüşmek isterim.',
                'status' => 'new',
            ]
        );

        // Canlı maç verileri
        LiveMatch::query()->firstOrCreate([
            'title' => 'Galatasaray vs Fenerbahçe',
            'home_team' => 'Galatasaray',
            'away_team' => 'Fenerbahçe',
        ], [
            'match_date' => now()->addHours(2),
            'home_score' => null,
            'away_score' => null,
            'is_live' => true,
            'is_finished' => false,
            'league' => 'Süper Lig',
            'round' => '26',
        ]);

        LiveMatch::query()->firstOrCreate([
            'title' => 'Beşiktaş vs Trabzonspor',
            'home_team' => 'Beşiktaş',
            'away_team' => 'Trabzonspor',
        ], [
            'match_date' => now()->addHours(4),
            'home_score' => null,
            'away_score' => null,
            'is_live' => false,
            'is_finished' => false,
            'league' => 'Süper Lig',
            'round' => '26',
        ]);

        LiveMatch::query()->firstOrCreate([
            'title' => 'Real Madrid vs Barcelona',
            'home_team' => 'Real Madrid',
            'away_team' => 'Barcelona',
        ], [
            'match_date' => now()->subHours(2),
            'home_score' => 2,
            'away_score' => 1,
            'is_live' => false,
            'is_finished' => true,
            'league' => 'La Liga',
            'round' => '24',
        ]);

        $this->command->info('Test verileri başarıyla oluşturuldu!');
        $this->command->info('Email: oyuncu@test.com, takim@test.com, scout@test.com, menejer@test.com');
        $this->command->info('Şifre: Password123');
    }
}

