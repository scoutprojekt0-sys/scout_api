# Deployment Guide

## Prerequisites

- PHP 8.2+
- Composer
- PostgreSQL (production) or SQLite (local)
- Node.js 20+ (for asset build)

## Environment Variables

Copy `.env.example` to `.env` and configure:

### Required

```env
APP_NAME="Scout API"
APP_ENV=production
APP_KEY=base64:...  # Generate with: php artisan key:generate
APP_DEBUG=false
APP_URL=https://api.nextscout.com

DB_CONNECTION=pgsql
DB_HOST=your-db-host
DB_PORT=5432
DB_DATABASE=scout_api
DB_USERNAME=your-username
DB_PASSWORD=your-password

CORS_ALLOWED_ORIGINS=https://nextscout.com,https://www.nextscout.com
```

### Optional

```env
NEWS_FEED_URL=https://feeds.bbci.co.uk/sport/football/rss.xml
NEWS_FEED_SOURCE="Football Feed"
NEWS_FEED_TIMEOUT=6

STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

## Deployment Steps

### 1. Clone and Install

```bash
git clone https://github.com/scoutprojekt0-sys/scout_api.git
cd scout_api
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### 2. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with production values.

### 3. Run Migrations

```bash
php artisan migrate --force
```

### 4. Optimize

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 6. Start Queue Worker (if using queues)

```bash
php artisan queue:work --daemon
```

## Railway Deployment

Railway auto-detects Laravel projects. Configure:

1. Add Railway PostgreSQL plugin
2. Set environment variables in Railway dashboard
3. Deploy script is in `scripts/railway_deploy_staging.sh`

## Health Monitoring

- **Health endpoint**: `GET /up`
- **Ping endpoint**: `GET /api/ping`

Set up monitoring to check these endpoints every 5 minutes.

## Rollback

```bash
git checkout <previous-commit>
composer install --no-dev
php artisan migrate:rollback
php artisan config:cache
```

## Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] Strong `APP_KEY` generated
- [ ] Database credentials secured
- [ ] CORS origins restricted
- [ ] HTTPS enforced
- [ ] Rate limiting configured
- [ ] Logs monitored regularly
- [ ] Backups scheduled

## Troubleshooting

### 500 Error

Check logs: `storage/logs/laravel.log`

### Database Connection Failed

Verify `DB_*` environment variables and database server status.

### Permission Denied

Ensure `storage/` and `bootstrap/cache/` are writable:

```bash
chmod -R 775 storage bootstrap/cache
```
