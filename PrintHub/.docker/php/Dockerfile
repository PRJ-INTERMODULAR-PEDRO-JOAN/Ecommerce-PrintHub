FROM php:8.3-fpm

#Environment variables to configure php

ENV PHP_POST_MAX_SIZE 10M
ENV PHP_VERSION 8.3

RUN apt-get update

RUN apt-get install -y libzip-dev
RUN docker-php-ext-install zip pdo pdo_mysql

# Instal·lar dependències del sistema per a l'extensió 'gd'
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    # Configurar i instal·lar l'extensió 'gd'
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    # Netejar la caché de apt per a una imatge més lleugera
    && apt-get clean && rm -rf /var/lib/apt/lists/*


RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY --from=composer /usr/bin/composer /usr/bin/composer
