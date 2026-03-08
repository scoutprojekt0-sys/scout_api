param(
    [Parameter(Mandatory = $true)]
    [string]$FrontendBaseUrl
)

$ErrorActionPreference = "Stop"

function Test-Contains([string]$Path, [string]$Needle) {
    $url = $FrontendBaseUrl.TrimEnd('/') + $Path
    Write-Host "Checking $url" -ForegroundColor Cyan
    $res = Invoke-WebRequest -Uri $url -Method Get -TimeoutSec 15 -UseBasicParsing
    if (-not $res.Content.Contains($Needle)) {
        throw "Expected '$Needle' not found at $url"
    }
}

Test-Contains "/" "NextScout"
Test-Contains "/index.html" "Veri Durumu"
Test-Contains "/professional-players.html" "Karsilastir"
Test-Contains "/player-profile.html" "Transfer Gecmisi"

Write-Host "Frontend smoke checks passed." -ForegroundColor Green

