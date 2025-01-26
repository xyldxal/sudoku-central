#!/bin/bash
# vercel-build.sh

# Install Python
apt-get update && apt-get install -y python3 python3-pip

# Make python directory executable
chmod -R 755 python/

# Continue with Laravel build
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache