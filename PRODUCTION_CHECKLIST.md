# NextScout Production Checklist

Bu checklist, `scout_api` backend'ini production ortamina cikarmadan once son kontrol icin hazirlanmistir.

## 1) Ortam ve Config

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL` dogru domain
- `FRONTEND_URL` ve `CORS_ALLOWED_ORIGINS` production domainleri
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` dogru
- `SESSION_DRIVER=redis` (onerilen)
- `CACHE_STORE=redis` (onerilen)
- `QUEUE_CONNECTION=redis` (onerilen)
- `MAIL_*` ayarlari gercek servis ile dogru
- `SANCTUM_STATEFUL_DOMAINS` ve cookie/domain ayarlari dogru
- `LOG_CHANNEL=stack`, `LOG_STACK=daily`, `LOG_LEVEL>=info`
- Gereksiz PHP extension'lar kapali (startup warning yok)
  - `oci8`, `pdo_oci`, `ibm_db2`, `pdo_ibm`, `pdo_informix` kullanilmiyorsa `php.ini`'den disable

## 2) Guvenlik

- `APP_KEY` set ve gizli
- Sunucuda `.env` dosyasi repo disi ve yetki kisitli
- `storage/` ve `bootstrap/cache/` yazma izinleri dogru
- HTTPS zorunlu, ters proxy/Nginx SSL tamam
- Rate limit ve auth middleware kritik endpointlerde aktif

## 3) Database

- Production backup alindi
- `php artisan migrate --force` dry-run mantigi ile onceden kontrol edildi
- Migration conflict yok (ayni tabloyu tekrar create etme yok)
- Kritik tablolarda indexler aktif

## 4) Runtime

- Queue worker/supervisor aktif
- `ops/systemd/nextscout-queue.service` veya esdeger supervisor config aktif
- `php artisan queue:restart` sonrasi worker geri kalkiyor
- Scheduler aktif (`* * * * * php artisan schedule:run`)
- `ops/systemd/nextscout-scheduler.service` veya esdeger scheduler worker aktif
- Log rotasyonu aktif

## 5) Build ve Cache

- `composer install --no-dev --optimize-autoloader`
- `php artisan config:cache`
- `php artisan route:cache`
- `php artisan view:cache`
- `php artisan event:cache`

## 6) Test ve Smoke

- `php artisan test` gecti
- `/api/ping` 200 donuyor
- `/api/health` 200 ve `checks.db=true`, `checks.cache=true`
- Kritik endpointler:
  - `/api/public/players`
  - `/api/public/players/{id}/profile`
  - `/api/club-needs`
  - `/api/trending/week`
- Frontend baglantilari:
  - `player-profile.html` ek paneller veri cekiyor
  - `professional-players.html` compare auth varsa API'den donuyor
- Frontend smoke:
  - `scripts/smoke-frontend.sh` veya `scripts/smoke-frontend.ps1` gecti

## 7) Deploy Sonrasi Dogrulama

- Login/role yonlendirme akisi dogru
- Dashboardlar aciliyor (`team`, `manager`, `coach/scout`)
- Anasayfa veri kartlari doluyor
- Error loglarinda kritik exception yok
- CPU/RAM/queue backlog stabil

## 8) Rollback Plani

- Son stabil release tag hazir
- DB rollback icin policy belli (otomatik rollback yerine restore plani)
- Bakim modu ac/kapa adimlari net

## 9) Reverse Proxy

- `ops/nginx/nextscout-api.conf` referansina gore:
  - HTTPS redirect aktif
  - Security headers aktif
  - `client_max_body_size` uygulama limitleriyle uyumlu
  - Rate-limit politikasi aktif
