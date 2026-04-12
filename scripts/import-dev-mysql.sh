#!/usr/bin/env bash
# يمسح قاعدة بيانات dev الحالية (DROP + CREATE) ويستورد ملف SQL.
# التشغيل من جذر المشروع حيث يوجد .env و docker-compose.dev.yml:
#   bash scripts/import-dev-mysql.sh "./127_0_0_1 (1).sql"
#
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.dev.yml}"
DB_SERVICE="${DB_SERVICE:-db}"
ENV_FILE="${ENV_FILE:-.env}"
SQL_FILE="${1:?الاستخدام: bash scripts/import-dev-mysql.sh path/to/dump.sql}"

if [[ ! -f "$SQL_FILE" ]]; then
  echo "الملف غير موجود: $SQL_FILE" >&2
  exit 1
fi

if [[ ! -f "$ENV_FILE" ]]; then
  echo "ملف .env غير موجود في $ROOT" >&2
  exit 1
fi

get_env() {
  grep -E "^${1}=" "$ENV_FILE" 2>/dev/null | tail -1 | cut -d= -f2- | sed "s/^['\"]//;s/['\"]$//"
}

DB_DATABASE="$(get_env DB_DATABASE)"
DB_USERNAME="$(get_env DB_USERNAME)"
DB_PASSWORD="$(get_env DB_PASSWORD)"
DB_ROOT_PASSWORD="$(get_env DB_ROOT_PASSWORD)"
ROOT_PW="${DB_ROOT_PASSWORD:-$DB_PASSWORD}"

if [[ -z "$DB_DATABASE" || -z "$ROOT_PW" ]]; then
  echo "ناقص DB_DATABASE أو كلمة مرور الـ root في .env (DB_ROOT_PASSWORD أو DB_PASSWORD)." >&2
  exit 1
fi

compose() {
  if docker compose version >/dev/null 2>&1; then
    docker compose -f "$COMPOSE_FILE" "$@"
  else
    docker-compose -f "$COMPOSE_FILE" "$@"
  fi
}

echo "==> إعادة إنشاء قاعدة البيانات: $DB_DATABASE"
compose exec -T "$DB_SERVICE" mysql -uroot -p"$ROOT_PW" <<EOF
SET NAMES utf8mb4;
DROP DATABASE IF EXISTS \`$DB_DATABASE\`;
CREATE DATABASE \`$DB_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EOF

if [[ -n "${DB_USERNAME:-}" && "$DB_USERNAME" != "root" ]]; then
  compose exec -T "$DB_SERVICE" mysql -uroot -p"$ROOT_PW" <<EOF
GRANT ALL PRIVILEGES ON \`$DB_DATABASE\`.* TO \`$DB_USERNAME\`@\`%\`;
FLUSH PRIVILEGES;
EOF
fi

echo "==> استيراد: $SQL_FILE"
compose exec -T "$DB_SERVICE" mysql -uroot -p"$ROOT_PW" "$DB_DATABASE" <"$SQL_FILE"

echo "==> تم. تحقق من الاتصال: compose exec app php artisan migrate:status"
