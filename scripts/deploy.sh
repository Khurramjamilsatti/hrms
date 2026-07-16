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
git pull --ff-only origin "$BRANCH"

echo "==> Running migrations"
php artisan migrate --force --no-interaction

echo "==> Clearing caches"
php artisan optimize:clear

echo "==> Deploy complete"
