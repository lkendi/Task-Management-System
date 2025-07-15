# Use the official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && \
    apt-get install -y libzip-dev zip unzip git curl libonig-dev libpq-dev libpng-dev && \
    docker-php-ext-install pdo_mysql zip mbstring

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working dir
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node and build assets
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs && \
    npm install && \
    npm run build

# Give Apache proper permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 8080 for Railway
EXPOSE 8080

# Run migrations & start Apache on port 8080
CMD php artisan migrate --force && apache2-foreground -DFOREGROUND -k start -p 8080
