FROM php:8.2-apache

WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libonig-dev libpng-dev libxml2-dev nodejs npm \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd opcache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure Apache for Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Install Node dependencies and build assets
RUN npm install && npm run build

# Copy application files
COPY . .

# Set file permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Configure for Railway
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-enabled/*.conf
EXPOSE 8080

# Optimized startup command
CMD ["sh", "-c", "php artisan migrate --force && exec apache2-foreground"]
