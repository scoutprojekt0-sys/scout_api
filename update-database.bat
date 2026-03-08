@echo off
echo ==========================================
echo Scout API - Veritabani Guncelleme
echo ==========================================
echo.

echo [1/4] Migration dosyalari kontrol ediliyor...
php artisan migrate:status
echo.

echo [2/4] Veritabani yenileniyor ve migration'lar calistiriliyor...
php artisan migrate:fresh
echo.

echo [3/4] Test verileri yukleniyor...
php artisan db:seed
echo.

echo [4/4] Cache temizleniyor...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
echo.

echo ==========================================
echo TAMAMLANDI!
echo ==========================================
echo.
echo Test Kullanicilari:
echo - Oyuncu: oyuncu@test.com
echo - Takim: takim@test.com
echo - Scout: scout@test.com
echo - Menejer: menejer@test.com
echo Sifre: Password123
echo.
echo API baslat: php artisan serve
echo ==========================================
pause
