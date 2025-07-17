FROM php:8.2-apache

WORKDIR /var/www/html

# 1. Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    libxml2-dev \   
    libpq-dev \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libsodium-dev \
    default-mysql-client \
    default-libmysqlclient-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-freetype \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring zip exif pcntl bcmath gd zip sodium


# 2. Get composer, nodejs and npm
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash -&& \ 
    apt-get update && apt-get install -y nodejs && \
    npm install -g npm@latest


# 3. Set working directory
WORKDIR /var/www/html

#4. Copy application files
COPY . .

# 5. Set file permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# 6. Install dependencies and build
RUN composer install --no-dev --no-interaction --optimize-autoloader
RUN npm install && npm run build

# 7. Expose port
EXPOSE 8080

# 8. Run migrations and start server
RUN php artisan migrate --force
RUN php artisan db:seed --force
RUN php artisan serve --host=0.0.0.0 --port=8080
