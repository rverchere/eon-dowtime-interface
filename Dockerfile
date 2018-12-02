FROM php:7.2-apache

LABEL maintainer="Rémi VERCHERE <remi.verchere@axians.com>"

COPY ./bower_components /var/www/html
COPY ./css /var/www/html
COPY ./images /var/www/html
COPY ./include /var/www/html
COPY ./index.php /var/www/html

RUN apt-get update \
    && apt-get install -y libyaml-dev libcurl4-gnutls-dev \
    && pecl install yaml \
    && docker-php-ext-enable yaml \
    && docker-php-ext-install curl \
    && docker-php-source delete