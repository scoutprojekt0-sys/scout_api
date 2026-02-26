# Production Go-Live Checklist

## T-24h

1. Confirm latest code is merged to release branch.
2. Confirm CI required checks are green:
   - Tests
   - API Smoke
   - Security
   - Coverage
3. Verify backup exists and restore drill is documented:
   - `docs/runbooks/BACKUP_RESTORE_DR.md`

## T-2h

1. Validate production environment variables:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_KEY` is set
   - DB credentials
   - `LOG_CHANNEL=ops`
   - `ALERT_SLACK_WEBHOOK_URL`
2. Confirm health endpoints are reachable:
   - `/up`
   - `/health/live`
   - `/health/ready`

## Deployment Window

1. Deploy release.
2. Run:
   - `php artisan migrate --force`
   - `php artisan config:cache`
   - `php artisan route:cache`
3. Run smoke checks:
   - auth register/login/me
   - opportunities index/create
   - applications flow
   - contacts inbox/sent

## Post-Deploy (0-30 min)

1. Trigger `Health Monitor` workflow.
2. Confirm no critical errors in logs.
3. Confirm Slack alert channel receives no failure alerts.
4. Confirm language switch works:
   - `/lang/tr`
   - `/lang/en`
   - `/lang/de`
   - `/lang/es`

## Rollback Criteria

Rollback immediately if one of these occurs:

1. `/health/ready` returns non-200 for more than 5 minutes.
2. Login or register fails for valid requests.
3. Error rate rises above acceptable threshold.

Use rollback workflow:
- `.github/workflows/rollback-staging.yml` (staging)
- Prod rollback via provider release controls.
