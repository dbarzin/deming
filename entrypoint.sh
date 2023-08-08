#!/usr/bin/env bash

cd /var/www/deming
php artisan migrate:fresh --seed
php artisan key:generate
php artisan storage:link
php artisan db:seed --class=AttributeSeeder 
php artisan db:seed --class=DomainSeeder 
php artisan db:seed --class=MeasureSeeder
php artisan deming:generateTests
php artisan passport:install
php artisan serve --host 0.0.0.0 --port 8000 &

nginx -g "daemon off;"

