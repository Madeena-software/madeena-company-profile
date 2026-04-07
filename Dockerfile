FROM dunglas/frankenphp:php8.3

WORKDIR /app

# Install build/runtime dependencies.
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    default-mysql-client \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required by Laravel + Filament.
RUN install-php-extensions \
    pdo_mysql \
    bcmath \
    intl \
    gd \
    zip \
    pcntl \
    opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . /app

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm install \
    && npm run build \
    && php artisan filament:upgrade \
    && php artisan storage:link || true

RUN chown -R www-data:www-data /app \
    && chmod -R 775 /app/storage /app/bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "octane:frankenphp", "--host=0.0.0.0", "--port=8000"]
