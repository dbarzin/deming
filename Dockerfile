FROM nginx:bookworm

RUN apt update && apt dist-upgrade -y
RUN apt-get install -y --no-install-recommends \
    git \
    composer \
    php \
    php-cli \
    php-opcache \
    php-mysql \
    php-pgsql \
    php-zip \
    php-gd \
    php-mbstring \
    php-curl \
    php-xml \
    mariadb-server \
    cron \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN touch /etc/mailname
RUN echo "sender@yourdomain.org" > /etc/mailname
RUN echo "* * * * * root cd /var/www/deming && php artisan schedule:run >> /dev/null 2>&1" >> /etc/crontab
RUN useradd -ms /bin/bash deming
RUN mkdir -p /var/www/deming

WORKDIR /var/www/deming

RUN git clone https://www.github.com/dbarzin/deming .
RUN cp docker/deming.conf /etc/nginx/conf.d/default.conf
RUN cp docker/userdemo.sh /etc/userdemo.sh
COPY docker/resetdb.sh /etc/resetdb.sh
RUN cp docker/uploadiso27001db.sh /etc/uploadiso27001db.sh
COPY docker/initialdb.sh /etc/initialdb.sh
RUN chmod +x /etc/*.sh
RUN mkdir -p storage/framework/views && mkdir -p storage/framework/cache && mkdir -p storage/framework/sessions && mkdir -p bootstrap/cache
RUN chmod -R 775 /var/www/deming/storage && chown -R www-data:www-data /var/www/deming
RUN composer install
RUN php artisan vendor:publish --all

RUN cp .env.example .env
RUN sed -i 's/DB_HOST=127\.0\.0\.1/DB_HOST=mysql/' .env

COPY docker/entrypoint.sh /opt/entrypoint.sh
RUN chmod u+x /opt/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/opt/entrypoint.sh"]
