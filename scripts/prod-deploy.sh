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
export IMAGE_TAG="$TAG"

docker pull "$IMAGE"
docker compose -f "$COMPOSE" pull
docker compose -f "$COMPOSE" up -d

sleep 10
docker exec "$CONTAINER" php artisan key:generate --force
docker exec "$CONTAINER" php artisan storage:link
docker exec "$CONTAINER" php artisan optimize:clear

echo "✅ Prod deploy done."
