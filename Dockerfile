# Use the official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && \
    apt-get install -y \
    libzip-dev zip unzip git curl libonig-dev \
    libpq-dev libpng-dev nodejs npm && \
    docker-php-ext-install pdo_mysql zip mbstring

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Build frontend assets
RUN npm install && npm run build

# Clear Laravel caches
RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

# Give Apache permissions to storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Fix Apache to point to /public and listen on 8080
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    echo "Listen 8080" >> /etc/apache2/ports.conf && \
    sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's/80/8080/g' /etc/apache2/sites-available/000-default.conf

# Expose port 8080
EXPOSE 8080

# Run migrations & start Apache
CMD php artisan migrate --force && apache2-foreground
