FROM php:8.1.0-fpm

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
    mv composer.phar /usr/local/bin/composer

RUN docker-php-ext-install pdo_mysql

# Install dependencies for GD, opcache and install GD with support for jpeg, png webp and freetype
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN apt install -y libicu-dev && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install opcache intl

# Install nodejs
RUN curl -sL https://deb.nodesource.com/setup_19.x -o nodesource_setup.sh && bash nodesource_setup.sh && apt-get -y --force-yes install nodejs

# Upgrading Node
RUN npm cache clean -f
RUN npm install -g n
RUN n stable
