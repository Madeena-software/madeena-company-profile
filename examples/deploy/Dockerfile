# ─────────────────────────────────────────────────────────────────────────────
# Madeena Workspace — Production Dockerfile
# PHP 8.3 FPM + Nginx served by Supervisor in a single container.
#
# The CI runner pre-builds assets (composer install --no-dev, npm run build)
# before calling `docker build`, so vendor/ and public/build/ are available
# in the build context.  The image itself therefore has no build-tool overhead.
# ─────────────────────────────────────────────────────────────────────────────
FROM php:8.3-fpm

LABEL maintainer="Madeena Software"
LABEL description="Madeena Workspace — Laravel 12 + Filament v3"

# Build-time argument for embedding version into the image
ARG APP_VERSION=dev
ENV APP_VERSION=${APP_VERSION}

# ── System packages ───────────────────────────────────────────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx \
        supervisor \
        curl \
        zip \
        unzip \
        git \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
        libicu-dev \
        libxml2-dev \
        libcurl4-openssl-dev \
        zlib1g-dev \
    && rm -rf /var/lib/apt/lists/*

# ── PHP extensions ────────────────────────────────────────────────────────────
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        dom \
        gd \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo \
        pdo_mysql \
        xml \
        zip

# ── PHP configuration ─────────────────────────────────────────────────────────
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php.ini "$PHP_INI_DIR/conf.d/99-custom.ini"

# ── Nginx configuration ───────────────────────────────────────────────────────
COPY docker/nginx.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default \
    && rm -f /etc/nginx/sites-enabled/000-default 2>/dev/null || true

# ── Supervisor configuration ──────────────────────────────────────────────────
RUN mkdir -p /var/log/supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ── Application ───────────────────────────────────────────────────────────────
WORKDIR /var/www/html

# Copy the full application (vendor/ and public/build/ are pre-built by CI)
COPY --chown=www-data:www-data . .

# Ensure required Laravel directories exist with correct permissions
RUN mkdir -p \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Ensure the VERSION file matches the build-time argument so runtime can
# read the baked-in version even when the repository's .git is absent.
RUN if [ -n "${APP_VERSION}" ]; then printf '%s' "${APP_VERSION}" > /var/www/html/VERSION || true; fi

# ── Entrypoint ────────────────────────────────────────────────────────────────
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
