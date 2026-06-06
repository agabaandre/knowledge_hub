# Link public/storage to the hub files root on Windows (PowerShell).
# Usage: .\link-hub-storage.ps1

$ErrorActionPreference = "Stop"
Set-Location $PSScriptRoot

if (-not (Test-Path "artisan")) {
    Write-Error "Run this script from the Knowledge Hub project root."
}

if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
    Write-Error "php is not on PATH."
}

if (-not (Test-Path "vendor\autoload.php")) {
    Write-Error "Run composer install first."
}

php artisan hub:link-storage
exit $LASTEXITCODE
