# syntax=docker/dockerfile:1.7
# ─────────────────────────────────────────────────────────────────────────────
# Madeena Company Profile — Production Dockerfile
# PHP 8.4 FPM (Alpine, digest-pinned) + Nginx served by Supervisor.
#
# The CI runner pre-builds assets (composer install --no-dev, npm run build)
# before calling `docker build`, so vendor/ and public/build/ are available
# in the build context. The image itself therefore has no build-tool overhead.
# ─────────────────────────────────────────────────────────────────────────────
ARG PHP_BASE=php:8.4.5-fpm-alpine3.21@sha256:5682435e64a0b2bd03337f2b9a92eacb8e095295377f3e2fa65eea15eae447b2
FROM ${PHP_BASE} AS base

LABEL maintainer="Madeena Software"
LABEL description="Madeena Company Profile — Laravel"

# Build-time argument for embedding version into the image
ARG APP_VERSION=dev
ENV APP_VERSION=${APP_VERSION}

# ── System packages ───────────────────────────────────────────────────────────
RUN set -eux; \
    apk add --no-cache \
        nginx \
        supervisor \
        curl \
        zip \
        unzip \
        git \
        icu-libs \
        libzip \
        libpng \
        libjpeg-turbo \
        freetype \
        oniguruma \
        zlib; \
    apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        oniguruma-dev \
        zlib-dev \
        linux-headers

# ── PHP extensions ────────────────────────────────────────────────────────────
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        gd \
        intl \
        opcache \
        pdo_mysql \
        zip \
    && pecl install igbinary redis \
    && docker-php-ext-enable igbinary redis opcache \
    && apk del .build-deps \
    && rm -rf /tmp/pear

# ── PHP configuration ─────────────────────────────────────────────────────────
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php.ini "$PHP_INI_DIR/conf.d/99-custom.ini"

# ── Nginx configuration ───────────────────────────────────────────────────────
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# ── Supervisor configuration ──────────────────────────────────────────────────
RUN mkdir -p /var/log/supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

FROM base AS production

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
