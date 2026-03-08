# Backup Strategy

## Automated Backups

### Database Backups

**Frequency**: Daily at 02:00 UTC

**Retention**:
- Daily backups: 7 days
- Weekly backups: 4 weeks
- Monthly backups: 6 months

**Location**: Encrypted cloud storage (S3 or equivalent)

**Backup Command** (PostgreSQL):
```bash
pg_dump -h $DB_HOST -U $DB_USERNAME -d $DB_DATABASE -F c -b -v -f backup_$(date +%Y%m%d_%H%M%S).dump
```

**Restore Command**:
```bash
pg_restore -h $DB_HOST -U $DB_USERNAME -d $DB_DATABASE -v backup_YYYYMMDD_HHMMSS.dump
```

### Application Files

**Frequency**: Weekly

**Includes**:
- Uploaded media files (`storage/app/public`)
- Configuration files (encrypted `.env`)
- Custom scripts

**Excludes**:
- `vendor/`
- `node_modules/`
- Cached files
- Log files

## Manual Backup

Before major deployments or migrations:

```bash
# Database
php artisan backup:database

# Full application snapshot
tar -czf scout_api_backup_$(date +%Y%m%d).tar.gz \
  --exclude='vendor' \
  --exclude='node_modules' \
  --exclude='storage/logs' \
  --exclude='storage/framework/cache' \
  .
```

## Testing Backups

**Frequency**: Monthly

**Process**:
1. Restore backup to staging environment
2. Verify data integrity
3. Test critical application functions
4. Document any issues

## Disaster Recovery

**RTO** (Recovery Time Objective): 2 hours  
**RPO** (Recovery Point Objective): 24 hours

**Recovery Steps**:
1. Provision new server infrastructure
2. Restore latest database backup
3. Deploy application code from Git
4. Restore uploaded files
5. Configure environment variables
6. Run health checks
7. Update DNS if needed

## Backup Monitoring

- Daily backup job completion alerts
- Backup file size anomaly detection
- Monthly test restoration reports

## Responsibilities

- **DevOps Team**: Backup automation and monitoring
- **Database Administrator**: Database backup validation
- **Product Owner**: Backup policy approval
