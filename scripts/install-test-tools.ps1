param()

$ErrorActionPreference = 'Stop'
$ProjectRoot = Split-Path -Parent $PSScriptRoot

Set-Location $ProjectRoot

if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
    throw 'PHP was not found in PATH.'
}

if (-not (Get-Command composer -ErrorAction SilentlyContinue)) {
    throw 'Composer was not found in PATH.'
}

composer install
if ($LASTEXITCODE -ne 0) {
    throw 'Composer install failed.'
}

if (-not (Test-Path '.env.testing')) {
    Copy-Item '.env.testing.example' '.env.testing'
}

if (-not (Test-Path 'database/testing.sqlite')) {
    New-Item -ItemType File 'database/testing.sqlite' | Out-Null
}

php artisan key:generate --env=testing --force
if ($LASTEXITCODE -ne 0) {
    throw 'Generating the testing APP_KEY failed.'
}

php artisan optimize:clear
if ($LASTEXITCODE -ne 0) {
    throw 'Clearing Laravel caches failed.'
}

Write-Host ''
Write-Host 'Testing tools are installed.' -ForegroundColor Green
Write-Host 'Safe default: database/testing.sqlite'
Write-Host 'Run: composer test:database'
