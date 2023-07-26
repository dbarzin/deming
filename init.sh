#!/usr/bin/env bash

cd /var/www/deming
php artisan migrate --seed
php artisan key:generate
php artisan storage:link
php artisan deming:generateTests
php artisan serve --host 0.0.0.0 --port 8000