# Base PHP image
FROM php:8.2-fpm

# Install dependencies + MySQL PHP extension
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql zip mbstring bcmath opcache intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy Laravel app
COPY . .

# Ensure storage/cache are writable
RUN mkdir -p storage bootstrap/cache && chmod -R 775 storage bootstrap/cache

# Install PHP dependencies (no dev)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Cache config, routes, views
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Expose port for Render
EXPOSE 10000

# Run Laravel with migrations
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
