FROM php:8.2-fpm

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git \
    cron \
    nginx \
    php-mysql \
    php-pgsql \
    php-zip \
    php-gd \
    php-mbstring \
    php-curl \
    php-xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/deming

# Cloner le dépôt Git
RUN git clone https://www.github.com/dbarzin/deming .

# Copier les fichiers de configuration
COPY docker/deming.conf /etc/nginx/conf.d/default.conf
COPY docker/userdemo.sh /etc/userdemo.sh
COPY docker/resetdb.sh /etc/resetdb.sh
COPY docker/uploadiso27001db.sh /etc/uploadiso27001db.sh
COPY docker/initialdb.sh /etc/initialdb.sh
COPY docker/entrypoint.sh /opt/entrypoint.sh

# Rendre les scripts exécutables
RUN chmod +x /etc/*.sh /opt/entrypoint.sh

# Configurer les permissions
RUN mkdir -p storage/framework/views storage/framework/cache storage/framework/sessions bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Installer les dépendances PHP
RUN composer install
RUN php artisan vendor:publish --all

# Configurer le fichier .env
RUN cp .env.example .env
RUN sed -i 's/DB_HOST=127\.0\.0\.1/DB_HOST=mysql/' .env

# Exposer le port 80
EXPOSE 80

# Démarrer Nginx et PHP-FPM
ENTRYPOINT ["/opt/entrypoint.sh"]
CMD ["nginx", "-g", "daemon off;"]
