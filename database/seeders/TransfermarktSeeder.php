<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Country;
use App\Models\Injury;
use App\Models\League;
use App\Models\LeagueStanding;
use App\Models\MatchDetail;
use App\Models\PlayerAttribute;
use App\Models\PlayerDetailedStatistic;
use App\Models\PlayerMarketValue;
use App\Models\PlayerProfile;
use App\Models\Position;
use App\Models\Season;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TransfermarktSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Transfermarkt Verileri Oluşturuluyor...');

        // 1. Ülkeler
        $this->command->info('📍 Ülkeler...');
        $turkey = Country::create(['name' => 'Türkiye', 'code' => 'TUR', 'fifa_code' => 'TUR']);
        $germany = Country::create(['name' => 'Almanya', 'code' => 'GER', 'fifa_code' => 'GER']);
        $england = Country::create(['name' => 'İngiltere', 'code' => 'ENG', 'fifa_code' => 'ENG']);
        $spain = Country::create(['name' => 'İspanya', 'code' => 'ESP', 'fifa_code' => 'ESP']);
        $france = Country::create(['name' => 'Fransa', 'code' => 'FRA', 'fifa_code' => 'FRA']);

        // 2. Pozisyonlar
        $this->command->info('⚽ Pozisyonlar...');
        $positions = [
            ['name' => 'Kaleci', 'short_name' => 'GK', 'category' => 'goalkeeper'],
            ['name' => 'Sol Bek', 'short_name' => 'LB', 'category' => 'defender'],
            ['name' => 'Stoper', 'short_name' => 'CB', 'category' => 'defender'],
            ['name' => 'Sağ Bek', 'short_name' => 'RB', 'category' => 'defender'],
            ['name' => 'Defansif Orta Saha', 'short_name' => 'CDM', 'category' => 'midfielder'],
            ['name' => 'Merkez Orta Saha', 'short_name' => 'CM', 'category' => 'midfielder'],
            ['name' => 'Sol Kanat', 'short_name' => 'LW', 'category' => 'forward'],
            ['name' => 'Sağ Kanat', 'short_name' => 'RW', 'category' => 'forward'],
            ['name' => 'Ofansif Orta Saha', 'short_name' => 'CAM', 'category' => 'midfielder'],
            ['name' => 'Santrafor', 'short_name' => 'ST', 'category' => 'forward'],
            ['name' => 'Merkez Forvet', 'short_name' => 'CF', 'category' => 'forward'],
        ];

        foreach ($positions as $pos) {
            Position::create($pos);
        }

        // 3. Ligler
        $this->command->info('🏆 Ligler...');
        $superLig = League::create([
            'country_id' => $turkey->id,
            'name' => 'Süper Lig',
            'short_name' => 'Süper Lig',
            'tier' => '1',
            'team_count' => 19,
        ]);

        $bundesliga = League::create([
            'country_id' => $germany->id,
            'name' => 'Bundesliga',
            'short_name' => 'Bundesliga',
            'tier' => '1',
            'team_count' => 18,
        ]);

        $premierLeague = League::create([
            'country_id' => $england->id,
            'name' => 'Premier League',
            'short_name' => 'EPL',
            'tier' => '1',
            'team_count' => 20,
        ]);

        // 4. Sezon
        $this->command->info('📅 Sezonlar...');
        $currentSeason = Season::create([
            'name' => '2025-2026',
            'start_date' => '2025-08-01',
            'end_date' => '2026-05-31',
            'is_current' => true,
        ]);

        $previousSeason = Season::create([
            'name' => '2024-2025',
            'start_date' => '2024-08-01',
            'end_date' => '2025-05-31',
            'is_current' => false,
        ]);

        // 5. Kulüpler
        $this->command->info('🏟️ Kulüpler...');
        $galatasaray = Club::create([
            'country_id' => $turkey->id,
            'league_id' => $superLig->id,
            'name' => 'Galatasaray',
            'short_name' => 'GS',
            'nickname' => 'Aslan',
            'stadium_name' => 'Rams Park',
            'stadium_capacity' => 52650,
            'city' => 'İstanbul',
            'founded_year' => 1905,
            'club_colors' => 'Sarı-Kırmızı',
            'total_market_value' => 250000000,
        ]);

        $fenerbahce = Club::create([
            'country_id' => $turkey->id,
            'league_id' => $superLig->id,
            'name' => 'Fenerbahçe',
            'short_name' => 'FB',
            'nickname' => 'Sarı Kanarya',
            'stadium_name' => 'Ülker Stadyumu',
            'stadium_capacity' => 47834,
            'city' => 'İstanbul',
            'founded_year' => 1907,
            'club_colors' => 'Sarı-Lacivert',
            'total_market_value' => 200000000,
        ]);

        $besiktas = Club::create([
            'country_id' => $turkey->id,
            'league_id' => $superLig->id,
            'name' => 'Beşiktaş',
            'short_name' => 'BJK',
            'nickname' => 'Kara Kartal',
            'stadium_name' => 'Vodafone Park',
            'stadium_capacity' => 41903,
            'city' => 'İstanbul',
            'founded_year' => 1903,
            'club_colors' => 'Siyah-Beyaz',
            'total_market_value' => 150000000,
        ]);

        // 6. Oyuncular (Detaylı)
        $this->command->info('👤 Oyuncular...');

        // Forvet Oyuncusu
        $striker = User::create([
            'name' => 'Burak Yılmaz',
            'email' => 'burak.yilmaz@test.com',
            'password' => Hash::make('Password123'),
            'role' => 'player',
            'city' => 'İstanbul',
        ]);

        PlayerProfile::create([
            'user_id' => $striker->id,
            'birth_year' => 1995,
            'date_of_birth' => '1995-07-15',
            'place_of_birth' => 'İstanbul',
            'position' => 'Santrafor',
            'primary_position_id' => 10,
            'current_club_id' => $galatasaray->id,
            'nationality_id' => $turkey->id,
            'dominant_foot' => 'Sağ',
            'preferred_foot' => 1,
            'height_cm' => 185,
            'weight_kg' => 80,
            'body_type' => 'athletic',
            'bio' => 'Genç ve yetenekli forvet oyuncusu',
            'current_team' => 'Galatasaray',
            'current_market_value' => 15000000,
            'highest_market_value' => 18000000,
            'contract_expires' => '2027-06-30',
            'jersey_number' => '9',
        ]);

        // Oyuncu Özellikleri
        PlayerAttribute::create([
            'player_user_id' => $striker->id,
            'pace' => 85,
            'shooting' => 88,
            'passing' => 72,
            'dribbling' => 75,
            'defending' => 35,
            'physicality' => 80,
            'finishing' => 90,
            'heading_accuracy' => 82,
            'shot_power' => 86,
            'positioning' => 89,
            'strengths' => ['Bitiricilik', 'Kafa Vuruşu', 'Pozisyon Alma'],
            'weaknesses' => ['Zayıf ayak', 'Savunma'],
        ]);

        // İstatistikler
        PlayerDetailedStatistic::create([
            'player_user_id' => $striker->id,
            'club_id' => $galatasaray->id,
            'season_id' => $currentSeason->id,
            'league_id' => $superLig->id,
            'appearances' => 25,
            'starts' => 23,
            'minutes_played' => 2100,
            'goals' => 18,
            'assists' => 5,
            'yellow_cards' => 3,
            'red_cards' => 0,
            'shots_on_target' => 45,
            'shots_off_target' => 20,
            'shot_accuracy' => 69.23,
            'average_rating' => 7.8,
            'man_of_the_match' => 6,
        ]);

        // Piyasa Değeri Geçmişi
        PlayerMarketValue::create([
            'player_user_id' => $striker->id,
            'market_value' => 10000000,
            'valuation_date' => '2024-01-01',
            'change_reason' => 'İyi performans',
        ]);

        PlayerMarketValue::create([
            'player_user_id' => $striker->id,
            'market_value' => 15000000,
            'valuation_date' => '2025-01-01',
            'change_reason' => 'Süper Lig'te harika performans',
        ]);

        // Transfer Geçmişi
        Transfer::create([
            'player_user_id' => $striker->id,
            'from_club_id' => $besiktas->id,
            'to_club_id' => $galatasaray->id,
            'season_id' => $currentSeason->id,
            'transfer_date' => '2025-07-01',
            'transfer_type' => 'transfer',
            'transfer_fee' => 12000000,
            'market_value_at_time' => 10000000,
            'is_confirmed' => true,
        ]);

        // 7. Puan Durumu
        $this->command->info('📊 Puan Durumu...');
        LeagueStanding::create([
            'league_id' => $superLig->id,
            'season_id' => $currentSeason->id,
            'club_id' => $galatasaray->id,
            'position' => 1,
            'played' => 28,
            'won' => 22,
            'drawn' => 4,
            'lost' => 2,
            'goals_for' => 68,
            'goals_against' => 22,
            'goal_difference' => 46,
            'points' => 70,
            'form' => 'WWDWW',
        ]);

        LeagueStanding::create([
            'league_id' => $superLig->id,
            'season_id' => $currentSeason->id,
            'club_id' => $fenerbahce->id,
            'position' => 2,
            'played' => 28,
            'won' => 20,
            'drawn' => 5,
            'lost' => 3,
            'goals_for' => 62,
            'goals_against' => 25,
            'goal_difference' => 37,
            'points' => 65,
            'form' => 'WDWWL',
        ]);

        $this->command->info('✅ Transfermarkt verileri başarıyla oluşturuldu!');
        $this->command->info('');
        $this->command->info('📊 Oluşturulan Veriler:');
        $this->command->info('  - Ülkeler: 5');
        $this->command->info('  - Pozisyonlar: 11');
        $this->command->info('  - Ligler: 3');
        $this->command->info('  - Sezonlar: 2');
        $this->command->info('  - Kulüpler: 3');
        $this->command->info('  - Oyuncular: 1 (detaylı)');
        $this->command->info('  - Transferler: 1');
        $this->command->info('  - Puan Durumu: 2');
    }
}
