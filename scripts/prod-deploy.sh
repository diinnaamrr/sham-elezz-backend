#!/usr/bin/env bash
# الافتراضي تاج main (فرع main على GHCR). لو محتاج تاج تاني: PROD_IMAGE_TAG=latest أو dev
#
#   sudo bash scripts/prod-deploy.sh
#   PROD_ROOT=/var/www/sham-elezz-prod bash scripts/prod-deploy.sh

set -euo pipefail

ROOT="${PROD_ROOT:-/var/www/sham-elezz-prod}"
COMPOSE="${COMPOSE_FILE:-docker-compose.prod.yml}"
TAG="${PROD_IMAGE_TAG:-main}"
IMAGE="${PROD_IMAGE:-ghcr.io/diinnaamrr/sham-el-ezz-backend:${TAG}}"
CONTAINER="${APP_CONTAINER:-sham_el_ezz_app_prod}"

cd "$ROOT"
if ! docker compose version >/dev/null 2>&1; then
  echo "❌ استخدم Docker Compose V2 فقط: sudo apt-get install -y docker-compose-plugin" >&2
  echo "   (docker-compose 1.x مع Docker حديث يسبب KeyError: ContainerConfig)" >&2
  exit 1
fi
export IMAGE_TAG="$TAG"

docker pull "$IMAGE"
docker compose -f "$COMPOSE" pull
docker compose -f "$COMPOSE" up -d

sleep 10
if ! docker exec -w /var/www/html "$CONTAINER" test -f artisan; then
  echo "❌ ملف artisan مش موجود: compose بيربط ./ على /var/www/html — لازم جذر المشروع على السيرفر فيه Laravel كامل (git pull أو نسخ من الصورة)." >&2
  exit 1
fi
docker exec -w /var/www/html "$CONTAINER" php artisan key:generate --force
docker exec -w /var/www/html "$CONTAINER" php artisan storage:link
docker exec -w /var/www/html "$CONTAINER" php artisan optimize:clear

echo "✅ Prod deploy done."
