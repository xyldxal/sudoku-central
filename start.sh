#!/bin/bash

# Install Python
apt-get update && apt-get install -y python3 python3-pip

# Make python directory executable
chmod -R 755 python/

# Start Laravel
php artisan serve --host=0.0.0.0 --port=$PORT