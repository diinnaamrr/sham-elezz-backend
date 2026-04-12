#!/usr/bin/env bash
# سحب صورة dev من GHCR ونسخ محتوى Laravel للمجلد الحالي (مع الإبقاء على storage و bootstrap و .env المحليين)
# ثم تشغيل docker-compose.dev وتشغيل أوامر artisan داخل sham_el_ezz_app_dev
#
# التشغيل من جذر المشروع: bash scripts/bootstrap-dev-from-image.sh
# على Windows: Git Bash أو WSL من مجلد المشروع

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

IMAGE="${IMAGE:-ghcr.io/diinnaamrr/sham-el-ezz-backend:dev}"
COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.dev.yml}"
APP_CONTAINER="${APP_CONTAINER:-sham_el_ezz_app_dev}"

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

echo "==> Start stack ($COMPOSE_FILE)"
if docker compose version >/dev/null 2>&1; then
  docker compose -f "$COMPOSE_FILE" up -d
else
  docker-compose -f "$COMPOSE_FILE" up -d
fi

echo "==> Wait for PHP container..."
sleep 10

echo "==> Artisan (inside $APP_CONTAINER)"
docker exec "$APP_CONTAINER" php artisan key:generate --force
docker exec "$APP_CONTAINER" php artisan storage:link
docker exec "$APP_CONTAINER" php artisan optimize:clear

echo "✅ تم. راجع إعدادات قاعدة البيانات في .env (DB_PASSWORD وغيرها)."
