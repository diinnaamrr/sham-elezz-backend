#!/usr/bin/env bash
# =============================================================================
# إنتاج — نفس الفكرة: Pull + نسخ من الصورة + compose + artisan
# الصورة الموحّدة على GHCR: ghcr.io/diinnaamrr/sham-el-ezz-backend:latest
# (Private package — لازم: echo TOKEN | docker login ghcr.io -u USER --password-stdin)
#
# تشغيل من جذر المشروع:
#   bash scripts/bootstrap-prod-from-image.sh
#
# أوامر يدوية (استبدل المسار لو مختلف):
#   cd /var/www/sham-elezz-prod
#   docker login ghcr.io -u diinnaamrr
#   docker pull ghcr.io/diinnaamrr/sham-el-ezz-backend:latest
#   docker run --rm -v "$(pwd):/target" ghcr.io/diinnaamrr/sham-el-ezz-backend:latest sh -c '
#     cd /var/www/html
#     find . -mindepth 1 -maxdepth 1 ! -name storage ! -name bootstrap ! -name .env -exec cp -a {} /target/ \;
#     cp -a vendor public /target/ 2>/dev/null || true
#   '
#   chmod -R 755 storage bootstrap/cache vendor public 2>/dev/null || true
#   bash scripts/ensure-laravel-storage-dirs.sh
#   docker compose -f docker-compose.prod.yml up -d
#   sleep 10
#   docker exec -w /var/www/html sham_el_ezz_app_prod php artisan key:generate --force
#   docker exec -w /var/www/html sham_el_ezz_app_prod php artisan storage:link
#   docker exec -w /var/www/html sham_el_ezz_app_prod php artisan optimize:clear
# =============================================================================

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

IMAGE="${IMAGE:-ghcr.io/diinnaamrr/sham-el-ezz-backend:${IMAGE_TAG:-latest}}"
COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.prod.yml}"
APP_CONTAINER="${APP_CONTAINER:-sham_el_ezz_app_prod}"

echo "==> Pull: $IMAGE"
docker pull "$IMAGE"

echo "==> Copy from image to $ROOT (preserve local storage, bootstrap, .env)"
docker run --rm -v "$ROOT:/target" "$IMAGE" sh -c "
  cd /var/www/html
  find . -mindepth 1 -maxdepth 1 ! -name 'storage' ! -name 'bootstrap' ! -name '.env' -exec cp -a {} /target/ \;
  cp -a vendor /target/ 2>/dev/null || true
  cp -a public /target/ 2>/dev/null || true
"

chmod -R 755 storage bootstrap/cache vendor public 2>/dev/null || true

echo "==> Ensure Laravel storage dirs (important if storage mount is partial)"
if [[ -f "$ROOT/scripts/ensure-laravel-storage-dirs.sh" ]]; then
  bash "$ROOT/scripts/ensure-laravel-storage-dirs.sh"
fi

echo "==> Start stack ($COMPOSE_FILE)"
if ! docker compose version >/dev/null 2>&1; then
  echo "❌ محتاج Docker Compose V2 (مش docker-compose 1.x القديم — يسبب KeyError: ContainerConfig)." >&2
  echo "   Ubuntu/Debian: sudo apt-get install -y docker-compose-plugin" >&2
  exit 1
fi
docker compose -f "$COMPOSE_FILE" up -d

echo "==> Wait for PHP container..."
sleep 10

echo "==> Artisan (inside $APP_CONTAINER)"
if ! docker exec -w /var/www/html "$APP_CONTAINER" test -f artisan; then
  echo "❌ artisan غير موجود بعد النسخ — راجع خطوة docker run أعلاه." >&2
  exit 1
fi
docker exec -w /var/www/html "$APP_CONTAINER" php artisan key:generate --force
docker exec -w /var/www/html "$APP_CONTAINER" php artisan storage:link
docker exec -w /var/www/html "$APP_CONTAINER" php artisan optimize:clear

echo "✅ تم. راجع .env (DB_*, APP_URL=https://..., وكلمة مرور قوية في الإنتاج)."
