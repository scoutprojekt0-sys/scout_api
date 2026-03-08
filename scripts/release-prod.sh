#!/usr/bin/env bash
set -euo pipefail

SKIP_TESTS="${SKIP_TESTS:-0}"
SKIP_COMPOSER_INSTALL="${SKIP_COMPOSER_INSTALL:-0}"

cd "$(dirname "$0")/.."

step() {
  echo ""
  echo "==> $1"
}

run_step() {
  echo "   $1"
  eval "$1"
}

step "PHP version"
run_step "php -v"

step "Maintenance mode ON"
run_step "php artisan down --render='errors::503' --retry=60"

cleanup() {
  step "Maintenance mode OFF"
  php artisan up || true
}
trap cleanup EXIT

if [[ "$SKIP_COMPOSER_INSTALL" != "1" ]]; then
  step "Install production dependencies"
  run_step "composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction"
fi

step "Generate optimized autoload"
run_step "composer dump-autoload -o --no-dev"

if [[ "$SKIP_TESTS" != "1" ]]; then
  step "Run tests"
  run_step "php artisan test --stop-on-failure"
fi

step "Migrate database"
run_step "php artisan migrate --force"

step "Clear and rebuild caches"
run_step "php artisan optimize:clear"
run_step "php artisan config:cache"
run_step "php artisan route:cache"
run_step "php artisan view:cache"
run_step "php artisan event:cache"

step "Queue and scheduler refresh"
run_step "php artisan queue:restart"
run_step "php artisan schedule:interrupt"

step "Basic health route check"
run_step "php artisan route:list --path=api/health"

step "Release completed"
echo "Production release script finished successfully."

