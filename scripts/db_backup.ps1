param(
    [string]$BackupDir = ""
)

$ErrorActionPreference = "Stop"

$root = Split-Path -Parent $PSScriptRoot
if ([string]::IsNullOrWhiteSpace($BackupDir)) {
    $BackupDir = Join-Path $root "backups"
}

$dbPath = $env:DB_DATABASE
if ([string]::IsNullOrWhiteSpace($dbPath)) {
    $dbPath = Join-Path $root "database\\database.sqlite"
} elseif (-not [System.IO.Path]::IsPathRooted($dbPath)) {
    $dbPath = Join-Path $root $dbPath
}

if (-not (Test-Path $dbPath)) {
    throw "Database file not found: $dbPath"
}

New-Item -ItemType Directory -Path $BackupDir -Force | Out-Null

$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$dest = Join-Path $BackupDir "backup_$timestamp.sqlite"
Copy-Item -Path $dbPath -Destination $dest -Force

Write-Output "Backup created: $dest"
