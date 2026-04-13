#!/usr/bin/env bash
# أنشئ هيكل storage/bootstrap اللازم لـ Laravel (مفيد بعد mount فارغ أو استنساخ بدون storage كامل).
# من جذر المشروع على السيرفر: bash scripts/ensure-laravel-storage-dirs.sh

set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

for sub in \
  storage/app/public \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/framework/testing \
  storage/logs \
  storage/tmp; do
  mkdir -p "$sub"
done
mkdir -p bootstrap/cache

echo "OK: Laravel storage/bootstrap dirs under $ROOT"
