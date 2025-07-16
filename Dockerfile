FROM php:8.2-apache

WORKDIR /var/www/html

# 1. Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libonig-dev libpng-dev libxml2-dev \
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# 2. Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd opcache

# 3. Enable Apache mod_rewrite
RUN a2enmod rewrite

# 4. Configure Apache for Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 5. Install Composer (CRITICAL FIX - moved before composer install)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 6. Copy ONLY dependency files first
COPY composer.json composer.lock package.json ./
# Copy correct Vite config (use .js or .ts based on your project)
COPY vite.config.ts ./

# 7. Install Composer dependencies
RUN composer install --no-dev --no-interaction --optimize-autoloader

# 8. Copy asset directories
COPY resources/css ./resources/css
COPY resources/js ./resources/js

# 9. Install Node dependencies and build
RUN npm install && npm run build

# 10. Copy all remaining application files
COPY . .

# 11. Set file permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# 12. Configure for Railway
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-enabled/*.conf
EXPOSE 8080

# 13. Health check (recommended)
HEALTHCHECK --interval=30s --timeout=3s \
    CMD curl -f http://localhost:8080 || exit 1

# 14. Optimized startup command
CMD ["sh", "-c", "php artisan migrate --force && exec apache2-foreground"]