FROM php:8.0-fpm

RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev libpng-dev zip libzip-dev zip \
    && docker-php-ext-install intl pdo pdo_mysql \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && docker-php-ext-install gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./docker/dev/php.ini-development /usr/local/etc/php/php.ini-development 

RUN apt install nodejs -y

RUN apt install npm -y

WORKDIR /var/www


