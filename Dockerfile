FROM php:8.2-apache

WORKDIR /var/www/html

# 1. Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libonig-dev libpng-dev libxml2-dev \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
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

# 5. Copy ONLY dependency files first
COPY composer.json composer.lock package.json ./
COPY vite.config.ts ./  

# 6. Install Composer dependencies
RUN /usr/local/bin/composer install --no-dev --no-interaction --optimize-autoloader

# 7. Copy asset directories
COPY resources/css ./resources/css
COPY resources/js ./resources/js

# 8. Install Node dependencies and build
RUN npm install && npm run build

# 9. Copy all remaining application files
COPY . .

# 10. Set file permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# 11. Configure for Railway
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-enabled/*.conf
EXPOSE 8080

# 12. Optimized startup command
CMD ["sh", "-c", "php artisan migrate --force && exec apache2-foreground"]