FROM php:7.0-apache

MAINTAINER Rémi VERCHERE <remi.verchere@axians.com>

RUN apt-get update \
    && apt-get install -y libyaml-dev libcurl4-gnutls-dev

RUN pecl install yaml \
    && docker-php-ext-enable yaml

RUN docker-php-ext-install curl


