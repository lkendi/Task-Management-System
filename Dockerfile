FROM php:8.2-apache

WORKDIR /var/www/html

# 1. First copy ONLY the required files for dependency installation
COPY composer.json composer.lock package.json vite.config.ts ./

# 2. Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libonig-dev libpng-dev libxml2-dev \
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# 3. Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd opcache

# 4. Install Composer dependencies
RUN composer install --no-dev --no-interaction --optimize-autoloader

# 5. Copy remaining files in separate stages
COPY resources/css ./resources/css
COPY resources/js ./resources/js

# 6. Install Node dependencies and build
RUN npm install && npm run build

# 7. Copy all remaining application files
COPY . .

# 8. Set file permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# 9. Configure for Railway
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-enabled/*.conf
EXPOSE 8080

# 10. Optimized startup command
CMD ["sh", "-c", "php artisan migrate --force && exec apache2-foreground"]
