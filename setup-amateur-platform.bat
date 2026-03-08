@echo off
echo ==========================================
echo AMATEUR FUTBOL PLATFORMU - Kurulum
echo ==========================================
echo.

echo Platform artik AMATOR FUTBOLCULAR icin optimize edildi!
echo.

echo [1/5] Transfermarkt migration'lari calistiriliyor...
php artisan migrate --path=database/migrations/2026_03_02_100001_create_transfermarkt_core_tables.php
php artisan migrate --path=database/migrations/2026_03_02_100002_create_transfermarkt_advanced_tables.php
php artisan migrate --path=database/migrations/2026_03_02_100003_enhance_player_team_profiles.php
echo.

echo [2/5] Amator futbol migration'i calistiriliyor...
php artisan migrate --path=database/migrations/2026_03_02_120001_create_amateur_football_tables.php
echo.

echo [3/5] Amator test verileri yukleniyor...
php artisan db:seed --class=AmateurFootballSeeder
echo.

echo [4/5] Genel test verileri yukleniyor...
php artisan db:seed
echo.

echo [5/5] Cache temizleniyor...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
echo.

echo ==========================================
echo AMATOR FUTBOL PLATFORMU HAZIR! ⚽
echo ==========================================
echo.
echo AMATOR OZELLIKLER:
echo ✓ Mahalle Takimlari
echo ✓ Deneme Maci Sistemi
echo ✓ Serbest Oyuncu Ilanlari
echo ✓ Video Portfoy
echo ✓ Topluluk Etkinlikleri
echo ✓ Hali Saha Takimlari
echo ✓ Pickup Maclari
echo.
echo Test Hesaplari:
echo - Emre Yildiz: emre.yildiz@test.com
echo - Mehmet Kara: mehmet.kara@test.com
echo Sifre: Password123
echo.
echo API baslat: php artisan serve
echo Dokumantasyon: AMATEUR_PLATFORM.md
echo ==========================================
pause
