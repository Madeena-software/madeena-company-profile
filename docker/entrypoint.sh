#!/bin/sh
set -e

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

# Cache configuration
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize
echo "Running optimization..."
php artisan optimize

echo "Startup complete. Starting application..."

# Execute the CMD
exec "$@"
