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

# RUN  docker-php-ext-install hash 
RUN  docker-php-ext-install iconv
RUN pecl install raphf propro 
RUN docker-php-ext-enable raphf propro
# RUN pecl install pecl_http 
RUN  echo -e "extension=raphf.so\nextension=propro.so\nextension=http.so" > /usr/local/etc/php/conf.d/docker-php-ext-http.ini 
RUN rm -rf /usr/local/etc/php/conf.d/docker-php-ext-raphf.ini 
RUN rm -rf /usr/local/etc/php/conf.d/docker-php-ext-propro.ini

RUN docker-php-ext-configure pcntl --enable-pcntl \
  && docker-php-ext-install \
    pcntl

RUN a2enmod rewrite
RUN chown -R www-data:www-data /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

RUN composer require --dev phpunit/phpunit spatie/phpunit-watcher
RUN composer dumpautoload
