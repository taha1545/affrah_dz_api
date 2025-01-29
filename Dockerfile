# Stage 1: Build
FROM php:8.1-apache AS build

# Install dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mysqli mbstring zip gd opcache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy custom PHP configuration for performance
COPY ./php.ini /usr/local/etc/php/conf.d/custom.ini

# Stage 2: Final image
FROM php:8.1-apache

# Copy PHP extensions and configuration from the build stage
COPY --from=build /usr/local/etc/php /usr/local/etc/php
COPY --from=build /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=build /etc/apache2/mods-enabled/rewrite.load /etc/apache2/mods-enabled/rewrite.load

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]