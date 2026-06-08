FROM php:8.2-fpm-bookworm

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip curl libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libicu-dev libxml2-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath exif gd intl mbstring opcache pcntl pdo_mysql zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-khub.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/10-opcache-khub.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-khub.conf

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

COPY . /var/www/html

ENV APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
