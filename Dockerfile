# Use PHP 8.1 with Apache as the base image
FROM php:8.1-apache

# Install required dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libonig-dev \
    curl \
    libpq-dev \
    libzip-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    mbstring \
    zip \
    gd \
    && docker-php-ext-configure gd --with-freetype --with-jpeg

# Increase file upload limits for PHP (Optional: Adjust based on your needs)
RUN echo "upload_max_filesize = 50M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 50M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "max_file_uploads = 20" >> /usr/local/etc/php/conf.d/uploads.ini

# Enable Apache mod_rewrite and configure overrides
RUN a2enmod rewrite && \
    echo '<Directory /var/www/html>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>' > /etc/apache2/conf-available/override.conf && \
    a2enconf override

# Install Composer (PHP dependency manager)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy application files into the container
COPY . .

# Install PHP dependencies using Composer
RUN composer install

# Set proper permissions for file upload directories
RUN mkdir -p /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html/uploads && \
    chmod -R 755 /var/www/html/uploads

# Expose port 80 to make the web server accessible
EXPOSE 80

# Set the entrypoint to start Apache in the foreground
CMD ["apache2-foreground"]
