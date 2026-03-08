# Production Checklist

Use this checklist before deploying to production.

## Security

- [ ] `APP_DEBUG=false` in production `.env`
- [ ] Strong `APP_KEY` generated with `php artisan key:generate`
- [ ] Database credentials are secure and not exposed
- [ ] `CORS_ALLOWED_ORIGINS` restricted to production domains only
- [ ] HTTPS enforced (redirect HTTP to HTTPS)
- [ ] Rate limiting tested (`RATE_LIMIT_AUTH`, `RATE_LIMIT_API`)
- [ ] Sanctum secure cookies enabled (`SANCTUM_COOKIE_SECURE=true`)
- [ ] Session secure cookie enabled (`SESSION_SECURE_COOKIE=true`)
- [ ] `.env` file not committed to repository
- [ ] No sensitive data in logs
- [ ] Stripe/PayPal keys are production keys (not sandbox)

## Database

- [ ] Production database connection configured (`DB_CONNECTION=pgsql`)
- [ ] Database migrations tested
- [ ] Database backups scheduled (daily minimum)
- [ ] Database indexes optimized
- [ ] Connection pooling configured if needed

## Performance

- [ ] Config cached: `php artisan config:cache`
- [ ] Routes cached: `php artisan route:cache`
- [ ] Views cached: `php artisan view:cache`
- [ ] Composer autoloader optimized: `composer install --optimize-autoloader --no-dev`
- [ ] Assets built: `npm run build`
- [ ] OPcache enabled
- [ ] Queue workers running if using queues

## Monitoring

- [ ] Health check endpoint tested (`/up`)
- [ ] Ping endpoint tested (`/api/ping`)
- [ ] Uptime monitoring configured (e.g., UptimeRobot, Pingdom)
- [ ] Error tracking configured (e.g., Sentry, Rollbar)
- [ ] Log monitoring configured
- [ ] Disk space monitoring configured
- [ ] Database connection monitoring configured

## Testing

- [ ] All unit tests pass: `php artisan test`
- [ ] All feature tests pass
- [ ] Code style check passes: `vendor/bin/pint --test`
- [ ] Postman E2E collection runs successfully
- [ ] Manual smoke test of critical flows completed
- [ ] Load testing completed (if expecting high traffic)

## Documentation

- [ ] API documentation up to date (`docs/API.md`)
- [ ] Deployment guide reviewed (`docs/DEPLOYMENT.md`)
- [ ] Environment variables documented (`.env.example`)
- [ ] Postman collection updated
- [ ] CHANGELOG updated

## Infrastructure

- [ ] DNS configured correctly
- [ ] SSL certificate installed and valid
- [ ] Firewall rules configured
- [ ] Server resources adequate (CPU, RAM, disk)
- [ ] CDN configured if needed
- [ ] Email service configured (SMTP)
- [ ] File storage configured (local/S3)

## Compliance

- [ ] Privacy policy published
- [ ] Terms of service published
- [ ] GDPR compliance checked (if applicable)
- [ ] Data retention policy documented
- [ ] Security policy published (`SECURITY.md`)

## Rollback Plan

- [ ] Previous version tagged in Git
- [ ] Rollback procedure documented
- [ ] Database rollback tested
- [ ] Rollback can be executed within 5 minutes

## Post-Deployment

- [ ] Verify all critical endpoints respond correctly
- [ ] Check error logs for unexpected issues
- [ ] Monitor response times
- [ ] Verify scheduled tasks are running
- [ ] Test user registration and login
- [ ] Test payment flow (if applicable)
- [ ] Notify team of successful deployment

## Emergency Contacts

Document emergency contacts for:
- DevOps team
- Database administrator
- Security team
- Product owner
