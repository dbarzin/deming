#!/bin/bash

echo "Initialize database"

# Migarte the database
php artisan migrate

# Create the admin user if it does not exist
php artisan db:seed --class=DatabaseSeeder
