param(
    [Parameter(Mandatory = $true)]
    [string]$BaseUrl
)

$ErrorActionPreference = "Stop"

function Test-Endpoint([string]$Path) {
    $url = ($BaseUrl.TrimEnd('/')) + $Path
    Write-Host "Checking $url" -ForegroundColor Cyan
    $null = Invoke-WebRequest -Uri $url -Method Get -TimeoutSec 15 -UseBasicParsing
}

Test-Endpoint "/api/ping"
Test-Endpoint "/api/health"
Test-Endpoint "/api/public/players?limit=5"
Test-Endpoint "/api/club-needs?limit=5"
Test-Endpoint "/api/trending/week"

Write-Host "Smoke checks passed." -ForegroundColor Green

