FROM php:8.2-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libonig-dev libpq-dev libpng-dev libxml2-dev nodejs npm \
  && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd opcache

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
  && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader \
  && npm install \
  && npm run build

RUN chown -R www-data:www-data /var/www/html \
  && chmod -R 775 storage bootstrap/cache

RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf

EXPOSE 8080

CMD ["sh","-c","php artisan migrate --force && apache2-foreground"]
