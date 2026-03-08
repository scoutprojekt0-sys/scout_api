@echo off
echo ==========================================
echo TRANSFERMARKT SEVİYESİ - Veritabani Guncelleme
echo ==========================================
echo.

echo [1/5] Onceki migration'lari kontrol ediliyor...
php artisan migrate:status
echo.

echo [2/5] Yeni Transfermarkt migration'lari calistiriliyor...
php artisan migrate
echo.

echo [3/5] Transfermarkt test verileri yukleniyor...
php artisan db:seed --class=TransfermarktSeeder
echo.

echo [4/5] Normal test verileri de yukleniyor...
php artisan db:seed
echo.

echo [5/5] Cache temizleniyor...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
echo.

echo ==========================================
echo TRANSFERMARKT UPGRADE TAMAMLANDI! 🏆
echo ==========================================
echo.
echo Yeni Ozellikler:
echo - Transfer Sistemi
echo - Piyasa Degeri Takibi
echo - Kulup Yonetimi
echo - Lig ve Puan Durumu
echo - Detayli Istatistikler
echo - Yaralanma Takibi
echo - Oyuncu Karsilastirma
echo.
echo Kulüpler: Galatasaray, Fenerbahce, Besiktas
echo Ligler: Super Lig, Bundesliga, Premier League
echo.
echo API baslat: php artisan serve
echo Dokumantasyon: TRANSFERMARKT_UPGRADE.md
echo ==========================================
pause
