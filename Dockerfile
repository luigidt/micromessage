FROM php:5.6-apache

RUN a2enmod rewrite \
  && apt-get update && apt-get install -y \
     libpq5 \
     libpq-dev \
  && docker-php-ext-install -j$(nproc) pdo_pgsql \
  && pecl install -o -f redis-2.2.8 \
  &&  rm -rf /tmp/pear \
  && mkdir -p /usr/local/etc/php

COPY docker/php.ini /usr/local/etc/php/
