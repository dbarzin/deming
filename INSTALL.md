# INSTALL Deming

## Ressources

Recommended configuration:

- OS : Ubunto 20.04 LTS
- RAM : 2G
- Disk : 50G
- VCPU : 2

## Git

Clone the project from GIT

    git clone github.com/dbarzin/deming

## Install PHP

Update you OS

sudo apt update && sudo apt upgrade

Install PHP

    sudo apt-get install php php-mysql

## Database

Install Mysql

    sudo apt-get install mysql-server

Create the database

    sudo mysql
    create database deming;
    CREATE USER 'deming_user'@'localhost' IDENTIFIED BY 'demPasssword-123';
    GRANT ALL ON deming.* TO deming_user@localhost;
    quit

Create tables

    php artisan migrate:fresh

## Data

Insert sample data

    mysql deming -p < data.sql

# Environement

Copy sample

    cp .env.sample .env

Configure environenement

    edit .env

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=deming
    DB_USERNAME=deming_user
    DB_PASSWORD=demPasssword-123

# Chache

If needed clear the cache

    php artisan cache:clear

Storage file link

    php artisan storage:link

