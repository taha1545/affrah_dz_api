# Use PHP 8.1 with Apache as the base image
FROM php:8.1-apache

# Install required dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mysqli mbstring zip gd

# Enable Apache mod_rewrite for clean URLs
RUN a2enmod rewrite

# Configure PHP file upload limits (Optional: Adjust as needed)
RUN echo "upload_max_filesize = 50M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 50M" >> /usr/local/etc/php/conf.d/uploads.ini

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy application files into the container
COPY . .

# Ensure proper permissions for uploaded files
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Expose port 80 for the Apache server
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
