FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    nginx \
    nodejs \
    npm \
    supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies without scripts
RUN composer install --no-dev --no-autoloader --no-scripts --no-interaction --prefer-dist

# Copy application code
COPY . .

# Generate autoloader and run package discovery
RUN composer dump-autoload --optimize && \
    php artisan package:discover --ansi

# Install Node.js dependencies and build frontend
RUN npm ci && npm run build

# Set storage and bootstrap/cache permissions
RUN chmod -R 775 storage bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Create nginx configuration
RUN rm /etc/nginx/sites-enabled/default
COPY docker/nginx.conf /etc/nginx/sites-available/app.conf
RUN ln -s /etc/nginx/sites-available/app.conf /etc/nginx/sites-enabled/app.conf

# Create supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port
EXPOSE 8080

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
