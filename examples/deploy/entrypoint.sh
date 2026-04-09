#!/bin/sh
set -e

cd /var/www/html

# If arguments are provided, run them directly (used for queue worker, scheduler, etc.)
if [ $# -gt 0 ]; then
    exec "$@"
fi

# ── Web server bootstrap ──────────────────────────────────────────────────────
echo "[entrypoint] Running Laravel bootstrap..."

# Wait briefly for DB to be ready (useful when containers start simultaneously)
if [ -n "$DB_HOST" ]; then
    echo "[entrypoint] Waiting for database at $DB_HOST:${DB_PORT:-3306}..."
    until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT:-3306};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
        sleep 2
    done
    echo "[entrypoint] Database is ready."
fi

# Create storage symlink
php artisan storage:link --force 2>/dev/null || true

# Run migrations
php artisan migrate --force

# Optimise for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[entrypoint] Bootstrap complete. Starting services..."

# PHP-FPM-only mode: used when a separate Nginx container handles HTTP
# Set PHP_FPM_ONLY=1 in the environment to enable (see docker-compose.prod.yml).
if [ "${PHP_FPM_ONLY:-0}" = "1" ]; then
    echo "[entrypoint] PHP_FPM_ONLY=1 — syncing public assets to shared volume..."
    cp -rT /var/www/html/public/. /var/www/public-files/
    echo "[entrypoint] Starting PHP-FPM..."
    exec /usr/local/sbin/php-fpm --nodaemonize
fi

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
