#!/usr/bin/env bash

cd /var/www/deming
bash /etc/resetdb.sh
bash /etc/initialdb.sh
php artisan storage:link
bash /etc/uploadiso27001db.sh
bash /etc/userdemo.sh
php artisan passport:install --force --quiet
php artisan key:generate
chown www-data:www-data storage/oauth-*.key
chmod 600 storage/oauth-*.key
php artisan serve --host 0.0.0.0 --port 8000 &
service postfix start
service cron start
nginx -g "daemon off;"
