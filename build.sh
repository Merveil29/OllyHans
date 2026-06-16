#!/usr/bin/env bash
set -e

echo "==> Installing Node.js..."
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt-get install -y nodejs

echo "==> Building frontend..."
cd /opt/render/project/src/frontend
npm ci
npm run build
cp -r dist /opt/render/project/src/ollyhans/public/dist

echo "==> Installing Composer dependencies..."
cd /opt/render/project/src/ollyhans
composer install --no-dev --optimize-autoloader

echo "==> Caching Laravel config..."
php artisan config:cache
php artisan route:cache

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Done!"