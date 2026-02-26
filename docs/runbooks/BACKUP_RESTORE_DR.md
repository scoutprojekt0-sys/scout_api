# Backup / Restore / DR Runbook

## Scope

Current backend database is SQLite (`database/database.sqlite`).
This runbook defines backup and restore steps for staging/production environments.

## Backup Frequency

- Minimum: daily backup
- Recommended: every 6 hours during active release periods
- Keep at least 7 daily backups and 4 weekly backups

## Backup Commands

Windows PowerShell (inside repo):

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\db_backup.ps1
```

Linux/macOS:

```bash
./scripts/db_backup.sh
```

Custom backup directory:

```bash
./scripts/db_backup.sh /var/backups/nextscout
```

## Restore Commands

Windows PowerShell:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\db_restore.ps1 -BackupFile .\backups\backup_YYYYMMDD_HHMMSS.sqlite
```

Linux/macOS:

```bash
./scripts/db_restore.sh backups/backup_YYYYMMDD_HHMMSS.sqlite
```

## Restore Validation Checklist

1. `php artisan optimize:clear`
2. `php artisan migrate --force`
3. `php artisan test --filter=HealthEndpointsTest`
4. Verify endpoints:
   - `/up`
   - `/health/live`
   - `/health/ready`

## Disaster Recovery (DR)

RTO target: 30 minutes  
RPO target: 6 hours

Incident flow:

1. Detect outage via Health Monitor alert.
2. Identify last known good backup.
3. Restore database using `db_restore.sh`.
4. Run restore validation checklist.
5. Keep platform in observation mode for 30 minutes.
6. Publish post-incident note with root cause and action items.
