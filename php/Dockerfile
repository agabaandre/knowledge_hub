# Use the official PHP 7.4 FPM image as the base image
FROM php:7.4-fpm

# Copy the php.ini file to the image
ADD php/php.ini /usr/local/etc/php/

# Install the PDO MySQL extension for PHP
RUN docker-php-ext-install pdo_mysql

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli