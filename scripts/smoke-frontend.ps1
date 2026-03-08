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

function Test-ContainsAny([string]$Path, [string[]]$Needles) {
    $url = $FrontendBaseUrl.TrimEnd('/') + $Path
    Write-Host "Checking $url" -ForegroundColor Cyan
    $res = Invoke-WebRequest -Uri $url -Method Get -TimeoutSec 15 -UseBasicParsing
    foreach ($needle in $Needles) {
        if ($res.Content.Contains($needle)) {
            return
        }
    }
    throw "Expected one of '$($Needles -join ', ')' not found at $url"
}

Test-Contains "/" "NextScout"
Test-Contains "/index.html" "Veri Durumu"
Test-Contains "/professional-players.html" "Karsilastir"
Test-ContainsAny "/player-profile.html" @("transferHistoryBody", "data-tab=`"transfer`"")

Write-Host "Frontend smoke checks passed." -ForegroundColor Green
