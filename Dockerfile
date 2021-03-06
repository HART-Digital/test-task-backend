FROM php:7.4-fpm-alpine3.11

WORKDIR "/var/www"

# Install git/vim/npm
RUN apk update && apk add --no-cache \
    git \
    vim \
    npm \
    bash \
    zsh \
    openssh

# Install required packages
RUN apk update && apk add --no-cache \
        postgresql-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        jpeg-dev \
        imagemagick \
        imagemagick-libs \
        imagemagick-dev \
        autoconf \
        g++ gcc make \
        libzip-dev zip

# Set up php
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        pdo_pgsql \
        pgsql \
        gd \
        json \
        zip \
    && pecl install imagick \
    && pecl install xdebug-3.0.0 \
    && pecl install -o -f redis \
    && docker-php-ext-enable \
        imagick \
        xdebug \
        redis \
    && rm -rf /tmp/pear

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --version

# Add non-root user
RUN addgroup -g 1000 -S www && \
    adduser -u 1000 -S www -G www

# Change current user to application
USER www
