#!/usr/bin/env sh
set -eu

mkdir -p /var/www/html/database
[ -f /var/www/html/database/database.sqlite ] || touch /var/www/html/database/database.sqlite

php artisan key:generate --force >/dev/null 2>&1 || true
php artisan config:clear >/dev/null 2>&1 || true
php artisan migrate --force

exec php artisan serve --host=0.0.0.0 --port=8000
