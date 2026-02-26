param(
    [Parameter(Mandatory = $true)]
    [string]$BackupFile
)

$ErrorActionPreference = "Stop"

$root = Split-Path -Parent $PSScriptRoot

if (-not [System.IO.Path]::IsPathRooted($BackupFile)) {
    $BackupFile = Join-Path $root $BackupFile
}

$dbPath = $env:DB_DATABASE
if ([string]::IsNullOrWhiteSpace($dbPath)) {
    $dbPath = Join-Path $root "database\\database.sqlite"
} elseif (-not [System.IO.Path]::IsPathRooted($dbPath)) {
    $dbPath = Join-Path $root $dbPath
}

if (-not (Test-Path $BackupFile)) {
    throw "Backup file not found: $BackupFile"
}

$dbDir = Split-Path -Parent $dbPath
New-Item -ItemType Directory -Path $dbDir -Force | Out-Null

Copy-Item -Path $BackupFile -Destination $dbPath -Force

Write-Output "Database restored from: $BackupFile"
Write-Output "Target database: $dbPath"
