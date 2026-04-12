# نسخة PowerShell لمشروع sham-el-ezz (dev)
# التشغيل من جذر المشروع: .\scripts\bootstrap-dev-from-image.ps1
#
# ملاحظة: خطوة النسخ من الحاوية تعتمد على sh داخل الصورة؛ على Windows يفضّل تشغيل
#   bash scripts/bootstrap-dev-from-image.sh
# إذا كان Git Bash متوفراً.

$ErrorActionPreference = "Stop"
$Root = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
Set-Location $Root

$Image = if ($env:IMAGE) { $env:IMAGE } else { "ghcr.io/diinnaamrr/sham-el-ezz-backend:dev" }
$ComposeFile = if ($env:COMPOSE_FILE) { $env:COMPOSE_FILE } else { "docker-compose.dev.yml" }
$AppContainer = if ($env:APP_CONTAINER) { $env:APP_CONTAINER } else { "sham_el_ezz_app_dev" }

Write-Host "==> Pull: $Image"
docker pull $Image

Write-Host "==> Copy from image to $Root"
# سطر واحد حتى يصل إلى sh -c كمعامل واحد بشكل موثوق على Windows
$copyCmd = "cd /var/www/html && find . -mindepth 1 -maxdepth 1 ! -name 'storage' ! -name 'bootstrap' ! -name '.env' -exec cp -a {} /target/ \; ; cp -a vendor /target/ 2>/dev/null ; true ; cp -a public /target/ 2>/dev/null ; true"
docker run --rm -v "${Root}:/target" $Image sh -c $copyCmd

if (Get-Command docker -ErrorAction SilentlyContinue) {
  $dcv = docker compose version 2>$null
  if ($LASTEXITCODE -eq 0) {
    docker compose -f $ComposeFile up -d
  } else {
    docker-compose -f $ComposeFile up -d
  }
} else {
  throw "docker not found in PATH"
}

Write-Host "==> Wait..."
Start-Sleep -Seconds 10

docker exec $AppContainer php artisan key:generate --force
docker exec $AppContainer php artisan storage:link
docker exec $AppContainer php artisan optimize:clear

Write-Host "Done. Check .env for DB_PASSWORD etc."
