FROM php:8.2-cli

# Install basic requirements
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN composer install --optimize-autoloader

# Set permissions
RUN chmod -R 777 storage bootstrap/cache

# Create .env file
RUN cp .env.example .env
RUN php artisan key:generate

# Expose port 8080
EXPOSE 8080

# Start PHP server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]