#!/bin/sh
set -e
# Bind-mounted ./storage from the host replaces the image's storage tree. If the host only has
# e.g. app/public/, Laravel paths like storage/logs are missing → "could not be created".
BASE=/var/www/html
for sub in \
  storage/app/public \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/framework/testing \
  storage/logs \
  storage/tmp; do
  mkdir -p "$BASE/$sub"
done
mkdir -p "$BASE/bootstrap/cache"

# Bind-mounted ./storage from the host often overrides image build-time chown; fix at start.
for d in "$BASE/storage" "$BASE/bootstrap/cache"; do
    if [ -d "$d" ]; then
        chown -R www-data:www-data "$d" 2>/dev/null || true
        chmod -R ug+rwX "$d" 2>/dev/null || true
    fi
done
exec docker-php-entrypoint "$@"
