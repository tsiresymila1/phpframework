FROM php:7.4-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
    apt-utils \
    git \
    gettext \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev 

# Xdebug
RUN pecl install  xdebug-3.0.3 \
    && docker-php-ext-enable xdebug

# Additionnal php extensions
RUN docker-php-ext-install pdo_mysql mysqli
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd
RUN docker-php-ext-enable xdebug
RUN docker-php-ext-install zip
RUN a2enmod rewrite
RUN chown -R www-data:www-data /var/www/html
WORKDIR /var/www/html