#!/bin/sh
set -e

cd /var/www/app

# Wait for database to be ready
echo "Waiting for MySQL to be ready..."
while ! nc -z mysql 3306; do
  sleep 1
done
echo "MySQL is ready!"

# Generate app key if not already set
if [ -z "$APP_KEY" ]; then
  echo "Generating app key..."
  php artisan key:generate --force
fi

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Seed database if specified
if [ "$DB_SEED" = "true" ]; then
  echo "Seeding database..."
  php artisan db:seed --force
fi

# Create storage link
echo "Creating storage link..."
php artisan storage:link || true

# Ensure writable runtime directories when storage/cache are bind-mounted.
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

# Cache configuration
echo "Caching configuration..."
php artisan config:cache
php artisan view:cache

echo "Startup complete. Starting application..."

# Execute the CMD
exec "$@"
