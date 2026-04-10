#!/bin/bash

echo "Initialize database"

php artisan migrate

if [ "${INITIAL_DB}" = "EN" ]; then
    php artisan db:seed  --class=DatabaseSeeder
elif [ "${INITIAL_DB}" = "FR" ]; then
    LANG=fr php artisan db:seed --class=DatabaseSeeder
else
    echo "WARNING: INITIAL_DB='${INITIAL_DB}' non reconnu (EN ou FR attendu)."
    exit 1
fi

