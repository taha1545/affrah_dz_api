# 
FROM php:8.1-apache AS build

# 
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mysqli mbstring zip gd opcache

# 
RUN a2enmod rewrite

# Copy custom PHP configuration for performance
COPY ./php.ini /usr/local/etc/php/conf.d/custom.ini

# 
FROM php:8.1-apache

# Copy PHP extensions and configuration from the build stage
COPY --from=build /usr/local/etc/php /usr/local/etc/php
COPY --from=build /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=build /etc/apache2/mods-enabled/rewrite.load /etc/apache2/mods-enabled/rewrite.load

# 
WORKDIR /var/www/html

#
COPY . .

# 
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# 
EXPOSE 80

# 
CMD ["apache2-foreground"]