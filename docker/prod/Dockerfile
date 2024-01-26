FROM php:8.2-fpm

RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev libpng-dev zip libzip-dev zip libzip-dev zip libfreetype6-dev libjpeg-dev \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

RUN pecl install imagick; \
    docker-php-ext-enable imagick;

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt install nodejs -y

RUN apt install npm -y

COPY php.ini /usr/local/etc/php/php.ini 
COPY nginx.conf /etc/nginx/nginx.conf

WORKDIR /var/www


