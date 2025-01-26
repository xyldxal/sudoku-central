FROM php:8.2-cli

# Install basic requirements
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    python3 \
    python3-pip

# Install PHP extensions required by Laravel
RUN docker-php-ext-install bcmath

# Set working directory
WORKDIR /var/www/html

# Copy composer files first
COPY composer.json composer.lock ./

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN composer install --no-scripts --no-autoloader

# Copy the rest of the application
COPY . .

# Generate autoload files
RUN composer dump-autoload --optimize

# Set permissions
RUN chmod -R 777 storage bootstrap/cache

# Create .env file
RUN cp .env.example .env
RUN php artisan key:generate

# Expose port 8080
EXPOSE 8080

# Start PHP server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]