#!/bin/sh
set -eu

export APP_KEY="${APP_KEY:-$(php artisan key:generate --show)}"
export APP_URL="${APP_URL:-${RENDER_EXTERNAL_URL:-http://localhost:10000}}"
export ASSET_URL="${ASSET_URL:-$APP_URL}"

php artisan migrate --force --seed
php artisan optimize

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
