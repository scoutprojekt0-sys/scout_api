# Deploy Checklist (Scout API)

## 1) Pre-Deploy

- `php -v` ve gerekli extension'lar aktif: `mbstring`, `openssl`, `pdo_sqlite` (veya hedef DB driver).
- `.env` production değerleri girildi:
  - `APP_ENV=production`
  - `APP_DEBUG=false`
  - `APP_URL`
  - `DB_*`
  - `FRONTEND_URL`
  - `CORS_ALLOWED_ORIGINS`
  - `NEWS_FEED_*`
- `APP_KEY` mevcut.

## 2) Build / Install

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

## 3) Database

```bash
php artisan migrate --force
```

## 4) Cache Optimize

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 5) Runtime

- Web server `public/` klasörünü serve ediyor.
- `storage` ve `bootstrap/cache` yazılabilir.
- Eğer media kullanılıyorsa:

```bash
php artisan storage:link
```

- Queue kullanılıyorsa worker aktif (`php artisan queue:work`).

## 6) Smoke Test

- `GET /up` -> 200
- `POST /api/auth/register` -> 201
- `POST /api/auth/login` -> 200
- `GET /api/news/live` -> 200
- Auth token ile `GET /api/players` -> 200

## 7) Rollback Plan

- Deploy öncesi release etiketi/commit kaydı alın.
- Migration geri alma gerekirse:

```bash
php artisan migrate:rollback --step=1
```

- Eski release'e dön ve cache'i temizle:

```bash
php artisan optimize:clear
```
