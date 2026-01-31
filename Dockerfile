# -------------------------
# Base PHP image
# -------------------------
FROM php:8.2-fpm

# -------------------------
# Install system dependencies
# -------------------------
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql zip mbstring bcmath

# -------------------------
# Install Composer
# -------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# -------------------------
# Set working directory
# -------------------------
WORKDIR /app

# -------------------------
# Copy application files
# -------------------------
COPY . .

# -------------------------
# Install PHP dependencies
# -------------------------
RUN composer install --no-dev --optimize-autoloader

# -------------------------
# Clear caches
# -------------------------
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# -------------------------
# Expose port
# -------------------------
EXPOSE 10000

# -------------------------
# Start Laravel
# -------------------------
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
