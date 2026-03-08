# Scout API

Scout API is a Laravel-based REST API powering the NextScout platform for football talent discovery, player-club connections, and opportunity management.

## Features

- **Authentication**: Sanctum-based token authentication with role-based access
- **Player Management**: Player profiles, stats, media uploads
- **Discovery**: Public player search, trending players, club needs
- **Opportunities**: Job/trial postings and application management
- **Billing**: Subscription plans, payment tracking, invoicing
- **Communication**: Contact messaging, notifications
- **Live Data**: News feed aggregation, live match counts

## Tech Stack

- PHP 8.2+
- Laravel 12
- SQLite (local) / PostgreSQL (production)
- Laravel Sanctum for API authentication
- Laravel Pint for code style

## License

MIT

## Scout API Quick Start

Tek komut ile ilk kurulum:

```bash
composer run setup
```

Ilk acilis dogrulamasi:

```bash
composer run verify:first-run
```

Not: `vendor/` klasoru repoda tutulmaz; `composer install` veya `composer run setup` ile lokal ortamda olusur.

Gelistirme sunucusu:

```bash
php artisan serve
```

E2E akis icin Postman collection:

- postman/Scout_API_E2E.postman_collection.json

Not: Media - Upload istegi icin media_file_path degiskenine lokal bir dosya yolu verin.

Newman ile collection calistirma:

```bash
npm run test:api
```

Local override ile calistirma:

```bash
npm run test:api:local
```

Windows'ta php.ini kullanarak test kosma (mbstring dahil):

```powershell
$env:PHPRC='C:\Users\Hp\PhpstormProjects\untitled'; php artisan test
```

## Live News Feed

`GET /api/news/live` artik dis RSS/Atom kaynagini okuyabilir.

Environment degiskenleri:

- `NEWS_FEED_URL` (ornek: `https://feeds.bbci.co.uk/sport/football/rss.xml`)
- `NEWS_FEED_SOURCE` (ornek: `Football Feed`)
- `NEWS_FEED_TIMEOUT` (saniye)

Kaynak ulasilamazsa endpoint otomatik olarak:
1. acik opportunities kayitlarina duser
2. onlar da yoksa statik fallback haber dondurur

## Operations

- `docs/runbooks/CI_RULESET_ALIGNMENT.md`: required check names, ruleset alignment, and PR merge-block recovery steps.

## Documentation

- `docs/API.md`: Complete API endpoint documentation
- `docs/DEPLOYMENT.md`: Production deployment guide
- `docs/PRODUCTION_CHECKLIST.md`: Pre-launch checklist
- `docs/onboarding/LOCAL_SETUP.md`: Local development setup

## Project maintenance

- `CONTRIBUTING.md`
- `SECURITY.md`

## Sales

- `docs/sales/PRODUCT_ONE_PAGER.md`
- `docs/sales/DEMO_SCRIPT_10_MIN.md`
- `docs/sales/PACKAGES_AND_PRICING_DRAFT.md`
- `docs/sales/SECURITY_AND_LAUNCH_APPENDIX.md`
