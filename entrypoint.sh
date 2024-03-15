#!/usr/bin/env bash

cd /var/www/deming
bash /etc/resetdb.sh
bash /etc/initialdb.sh
php artisan key:generate
php artisan storage:link
bash /etc/uploadiso27001db.sh
bash /etc/userdemo.sh
php artisan passport:install
php artisan serve --host 0.0.0.0 --port 8000 &

nginx -g "daemon off;"

