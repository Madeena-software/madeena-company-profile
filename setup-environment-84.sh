#!/usr/bin/env bash
set -euo pipefail

echo "==> Purging legacy PHP 8.1/8.2/8.3"
sudo apt-get update
sudo apt-get purge -y 'php8.1*' 'php8.2*' 'php8.3*' || true
sudo apt-get autoremove -y

echo "==> Installing PHP 8.4 + required extensions"
sudo add-apt-repository -y ppa:ondrej/php
sudo apt-get update
sudo apt-get install -y \
  software-properties-common \
  php8.4 php8.4-cli php8.4-fpm php8.4-common php8.4-dev \
  php8.4-mysql php8.4-bcmath php8.4-gd php8.4-zip php8.4-intl \
  php8.4-opcache php8.4-igbinary php8.4-redis php8.4-mbstring \
  php8.4-xml php8.4-curl unzip curl git composer

echo "==> Installing Node.js 22"
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt-get install -y nodejs

echo "==> Setting php alternatives"
sudo update-alternatives --set php /usr/bin/php8.4
sudo update-alternatives --set phar /usr/bin/phar8.4 || true
sudo update-alternatives --set phar.phar /usr/bin/phar.phar8.4 || true

echo "==> Verifying runtime"
php -v
php -r 'exit(PHP_VERSION_ID>=80400?0:1);'
php -m | grep -Ei 'pdo_mysql|bcmath|gd|zip|intl|opcache|igbinary|redis'

echo "==> Verifying Composer uses PHP 8.4"
composer --version
composer diagnose || true
echo "==> Verifying Node.js"
node -v
npm -v

echo "✅ Environment aligned to PHP 8.4 & Node.js 22"
