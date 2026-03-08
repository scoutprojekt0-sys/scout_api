@echo off
chcp 65001 > nul
echo.
echo ╔════════════════════════════════════════════════════════════╗
echo ║   ⚽🏀🏐  MULTI-SPORT PLATFORM KURULUMU  🏐🏀⚽            ║
echo ║                                                            ║
echo ║  Futbol • Basketbol • Voleybol (Bay + Bayan)            ║
echo ╚════════════════════════════════════════════════════════════╝
echo.

echo [1/6] Tüm migration'lar çalıştırılıyor...
php artisan migrate
echo.

echo [2/6] Multi-sport test verileri yükleniyor...
php artisan db:seed --class=MultiSportSeeder
echo.

echo [3/6] Amatör futbol verileri yükleniyor...
php artisan db:seed --class=AmateurFootballSeeder
echo.

echo [4/6] Diğer test verileri yükleniyor...
php artisan db:seed
echo.

echo [5/6] Cache temizleniyor...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
echo.

echo [6/6] Veritabanı durumu kontrol ediliyor...
php artisan migrate:status
echo.

echo ╔════════════════════════════════════════════════════════════╗
echo ║     ✅ MULTI-SPORT PLATFORM KURULUMU TAMAMLANDI! ✅        ║
echo ╚════════════════════════════════════════════════════════════╝
echo.
echo 📊 DESTEKLENEN SPORLAR:
echo   ⚽ FUTBOL - Bay / Bayan / Karma Takımlar
echo   🏀 BASKETBOL - Bay / Bayan / Karma Takımlar
echo   🏐 VOLEYBOL - Bay / Bayan / Karma Takımlar
echo.
echo 🎯 ÖZELLİKLER:
echo   ✓ Cinsiyet Bazlı Takım Arayışı
echo   ✓ Spor-Spesifik İstatistikler
echo   ✓ Leaderboard Sistemi
echo   ✓ Multi-Sport Etkinlikler
echo   ✓ Tercih Yönetimi
echo.
echo 🧪 TEST HESAPLARI:
echo.
echo   ⚽ FUTBOL:
echo      Bay: ahmet.futbol@test.com
echo      Bayan: ayse.futbol@test.com
echo.
echo   🏀 BASKETBOL:
echo      Bay: mehmet.basketball@test.com
echo      Bayan: zeynep.basketball@test.com
echo.
echo   🏐 VOLEYBOL:
echo      Bay: emre.volleyball@test.com
echo      Bayan: seda.volleyball@test.com
echo.
echo   🔐 Tüm Şifreler: Password123
echo.
echo 🚀 API BAŞLATMA:
echo   php artisan serve
echo.
echo 📖 API Dokümantasyonu: MULTI_SPORT_PLATFORM.md
echo.
echo ╔════════════════════════════════════════════════════════════╗
echo ║  Platform hazır! Sunucuyu başlatmak için komutu çalıştırın║
echo ╚════════════════════════════════════════════════════════════╝
pause
