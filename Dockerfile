FROM php:8.2-cli

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    python3 \
    python3-pip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install mbstring exif pcntl bcmath gd

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chmod -R 777 storage bootstrap/cache python/

# Create .env file from example
RUN cp .env.example .env

# Generate Laravel key
RUN php artisan key:generate --force
RUN php artisan cache:clear 
RUN php artisan config:clear

# Create logs directory and set permissions
RUN mkdir -p storage/logs
RUN chmod -R 777 storage/logs

# Expose port 8080
EXPOSE 8080

# Start PHP server with error reporting enabled
ENTRYPOINT ["php", "-d", "display_errors=1", "-d", "error_reporting=E_ALL", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]