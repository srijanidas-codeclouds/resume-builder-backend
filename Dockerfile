# -------------------------
# Base image
# -------------------------
FROM php:8.2-fpm

# -------------------------
# System dependencies
# -------------------------
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    curl \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# -------------------------
# Install Composer
# -------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# -------------------------
# Set working directory
# -------------------------
WORKDIR /var/www

# -------------------------
# Copy composer files first (Docker cache optimization)
# -------------------------
COPY composer.json composer.lock ./

# -------------------------
# Install PHP dependencies (NO dev, optimized)
# -------------------------
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-progress

# -------------------------
# Copy application source
# -------------------------
COPY . .

# -------------------------
# Permissions (important for Laravel)
# -------------------------
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# -------------------------
# Switch user
# -------------------------
USER www-data

# -------------------------
# Expose PHP-FPM port
# -------------------------
EXPOSE 9000

# -------------------------
# Start PHP-FPM
# -------------------------
CMD ["php-fpm"]
