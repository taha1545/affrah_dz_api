FROM php:8.1-apache

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


RUN a2enmod rewrite && \
    echo '<Directory /var/www/html>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>' > /etc/apache2/conf-available/override.conf && \
    a2enconf override


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


WORKDIR /var/www/html


COPY . .

RUN composer install


EXPOSE 80
