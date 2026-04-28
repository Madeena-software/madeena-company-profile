#!/bin/sh
set -e

cd /var/www/html

# ── Database readiness check (all modes: web, queue, scheduler) ────────────
# Moved before the argument check so queue workers also wait for the DB
# before attempting to process jobs.
if [ -n "$DB_HOST" ]; then
    echo "[entrypoint] Waiting for database at $DB_HOST:${DB_PORT:-3306}..."
    _DB_WAIT=0
    until php -r "\$h=getenv('DB_HOST');\$p=getenv('DB_PORT')?:'3306';\$d=getenv('DB_DATABASE');\$u=getenv('DB_USERNAME');\$pw=getenv('DB_PASSWORD');new PDO(\"mysql:host=\$h;port=\$p;dbname=\$d\",\$u,\$pw);" 2>/dev/null; do
        _DB_WAIT=$((_DB_WAIT + 3))
        if [ "$((_DB_WAIT % 30))" -eq 0 ]; then
            echo "[entrypoint]   … waiting (${_DB_WAIT}s elapsed)"
        fi
        if [ "$((_DB_WAIT % 60))" -eq 0 ]; then
            echo "[entrypoint] ⚠️  Database not ready after ${_DB_WAIT}s — still waiting..."
            echo "[entrypoint]   DB_HOST=$DB_HOST DB_PORT=${DB_PORT:-3306} DB_DATABASE=$DB_DATABASE DB_USERNAME=$DB_USERNAME"
        fi
        sleep 3
    done
    echo "[entrypoint] ✅ Database is ready."
fi

# If arguments are provided, run them directly (used for queue worker, scheduler, etc.)
if [ $# -gt 0 ]; then
    exec "$@"
fi

# ── Web server bootstrap ──────────────────────────────────────────────────────
echo "[entrypoint] Running Laravel bootstrap..."

# Create storage symlink
php artisan storage:link --force 2>/dev/null || true

# Run migrations
php artisan migrate --force

# Seed the CMS content only when the site tables are still empty.
php artisan madeena:seed-cms --force

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