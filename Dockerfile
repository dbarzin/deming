FROM php:8.2-cli
RUN apt-get update && apt-get upgrade --assume-yes \
    && DEBIAN_FRONTEND=noninteractive apt-get install --assume-yes --no-install-recommends \
    libfreetype-dev libjpeg62-turbo-dev libpng-dev libonig-dev libcurl4-gnutls-dev \
    libxml++2.6-dev libzip-dev libpq-dev 
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mbstring curl xml zip opcache pdo_pgsql
    # php-mysql \

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN mkdir /var/www/html/deming
RUN chown $USER:$GROUP /var/www/html/deming

WORKDIR /var/www/html/deming

COPY . /var/www/html/deming/
RUN mkdir -p storage/framework/views storage/framework/cache storage/framework/sessions bootstrap/cache

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN cd /var/www/html/deming && composer install

CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port", "8000"]
