#!/bin/sh
set -e
# Bind-mounted ./storage from the host often overrides image build-time chown; fix at start.
for d in /var/www/html/storage /var/www/html/bootstrap/cache; do
    if [ -d "$d" ]; then
        chown -R www-data:www-data "$d" 2>/dev/null || true
        chmod -R ug+rwX "$d" 2>/dev/null || true
    fi
done
exec docker-php-entrypoint "$@"
