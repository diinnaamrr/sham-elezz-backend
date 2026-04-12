#!/usr/bin/env bash
# سحب صورة production من GHCR ونسخ كود Laravel للمجلد الحالي (مع الإبقاء على storage و bootstrap و .env)
# ثم تشغيل docker-compose.prod وتشغيل أوامر artisan داخل sham_el_ezz_app_prod
#
# التشغيل من جذر المشروع على السيرفر:
#   bash scripts/bootstrap-prod-from-image.sh
#
# تاج آخر (اختياري): IMAGE_TAG=latest أو IMAGE_TAG=dev

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

# إنتاج: تاج main من workflow الـ prod على فرع main
IMAGE="${IMAGE:-ghcr.io/diinnaamrr/sham-el-ezz-backend:${IMAGE_TAG:-main}}"
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
docker exec "$APP_CONTAINER" php artisan key:generate --force
docker exec "$APP_CONTAINER" php artisan storage:link
docker exec "$APP_CONTAINER" php artisan optimize:clear

echo "✅ تم. راجع .env (DB_*, APP_URL=https://..., وكلمة مرور قوية في الإنتاج)."
