FROM php:7.2-apache

MAINTAINER Rémi VERCHERE <remi.verchere@axians.com>

RUN apt-get update \
    && apt-get install -y libyaml-dev libcurl4-gnutls-dev

RUN pecl install yaml \
    && docker-php-ext-enable yaml \
    && docker-php-ext-install curl \
    && docker-php-source delete
