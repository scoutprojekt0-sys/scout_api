param(
    [switch]$SkipTests,
    [switch]$SkipComposerInstall
)

$ErrorActionPreference = "Stop"

function Step([string]$msg) {
    Write-Host ""
    Write-Host "==> $msg" -ForegroundColor Cyan
}

function Run-Step([string]$cmd) {
    Write-Host "   $cmd" -ForegroundColor DarkGray
    Invoke-Expression $cmd
}

Set-Location -Path $PSScriptRoot\..

Step "PHP version"
Run-Step "php -v"

Step "Maintenance mode ON"
Run-Step "php artisan down --render='errors::503' --retry=60"

try {
    if (-not $SkipComposerInstall) {
        Step "Install production dependencies"
        Run-Step "composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction"
    }

    Step "Generate optimized autoload"
    Run-Step "composer dump-autoload -o --no-dev"

    if (-not $SkipTests) {
        Step "Run tests"
        Run-Step "php artisan test --stop-on-failure"
    }

    Step "Migrate database"
    Run-Step "php artisan migrate --force"

    Step "Clear and rebuild caches"
    Run-Step "php artisan optimize:clear"
    Run-Step "php artisan config:cache"
    Run-Step "php artisan route:cache"
    Run-Step "php artisan view:cache"
    Run-Step "php artisan event:cache"

    Step "Queue and scheduler refresh"
    Run-Step "php artisan queue:restart"
    Run-Step "php artisan schedule:interrupt"

    Step "Basic health check"
    Run-Step "php artisan route:list --path=api/ping"
}
finally {
    Step "Maintenance mode OFF"
    Run-Step "php artisan up"
}

Step "Release completed"
Write-Host "Production release script finished successfully." -ForegroundColor Green

