#!/usr/bin/env bash
set -euo pipefail

# Bare-metal deploy helper (same steps as .github/workflows/ci.yml).
# Usage from the app root on the VPS:
#   ./scripts/deploy.sh

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

BRANCH="${1:-$(git rev-parse --abbrev-ref HEAD)}"

echo "==> Pulling latest code ($BRANCH)"
git fetch --all --prune
git checkout "$BRANCH"
git reset --hard "origin/$BRANCH"

echo "==> Running migrations"
php artisan migrate --force --no-interaction

echo "==> Syncing roles & permissions"
php artisan db:seed --class=RolesAndPermissionsSeeder --force --no-interaction

echo "==> Ensuring storage permissions"
mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache || true
if id www-data >/dev/null 2>&1; then
  chown -R www-data:www-data storage bootstrap/cache || true
fi
# Prevent a root-owned laravel.log from blocking the web user
if [ -f storage/logs/laravel.log ]; then
  chmod ug+rw storage/logs/laravel.log || true
fi

echo "==> Clearing caches"
php artisan optimize:clear

echo "==> Deploy complete"
