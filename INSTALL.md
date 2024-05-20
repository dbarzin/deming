# Deming installation procedure

## Recommended configuration

- OS : Ubuntu 22.04 LTS
- RAM : 2G
- Disk : 120G
- VCPU 2

## Installation

Update linux distribution

    sudo apt update && sudo apt upgrade

Install Apache, git, php and composer

    sudo apt-get install git composer apache2 php-fpm php php-cli php-opcache php-mysql php-zip php-gd php-mbstring php-curl php-xml -y

Create the project directory

    cd /var/www
    sudo mkdir deming
    sudo chown $USER:$GROUP deming

Clone project from Github

    git clone https://www.github.com/dbarzin/deming

Install packages with composer :

    cd deming
    mkdir -p storage/framework/views
    mkdir -p storage/framework/cache
    mkdir -p storage/framework/sessions
    mkdir -p bootstrap/cache
    composer install

Publish all publishable assets from vendor packages

    php artisan vendor:publish --all

## MySQL

Install MySQL

    sudo apt install mysql-server

Make sure you're using MySQL and not MariaDB (Deming doesn't work with MariaDB).

    sudo mysql --version

Run MySQL with root rights

    sudo mysql

Create database _deming_ and user _deming_user_.

    CREATE DATABASE deming CHARACTER SET utf8 COLLATE utf8_general_ci;
    CREATE USER 'deming_user'@'localhost' IDENTIFIED BY 'demPasssword-123';
    GRANT ALL ON deming.* TO deming_user@localhost;
    GRANT PROCESS ON *.* TO 'deming_user'@'localhost';

    FLUSH PRIVILEGES;
    EXIT;

## Configuration

Create an .env file in the project root directory:

    cd /var/www/deming
    cp .env.example .env

Set database connection parameters :

    vi .env

    ## .env file
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=deming
    DB_USERNAME=deming_user
    DB_PASSWORD=demPasssword-123


## Create database

Run migrations

    php artisan migrate --seed

Note: the seed is important (--seed), as it will create the first administrator user for you.

Generate application key

    php artisan key:generate

Create storage link

    php artisan storage:link

To import the database with 27001:2022 security measures

    php artisan db:seed --class=AttributeSeeder
    php artisan db:seed --class=DomainSeeder
    php artisan db:seed --class=MeasureSeeder

Generate test data (optional)

    php artisan deming:generateTests

Start application with php

    php artisan serve

or to access the application from another server

    php artisan serve --host 0.0.0.0 --port 8000

The application can be accessed at URL [http://127.0.0.1:8000]

    user : admin@admin.localhost
    password : admin

The administrator's default language is English. To change language, go to the user profile page
(top right of the main page).


## Apache

To configure Apache, modify the properties of the Deming directory and grant the appropriate permissions to the hive with the following command:

    sudo chown -R www-data:www-data /var/www/deming
    sudo chmod -R 775 /var/www/deming/storage

Next, create a new Apache virtual host configuration file to serve the application:

    sudo vi /etc/apache2/sites-available/deming.conf

Add the following lines:

    <VirtualHost *:80>
    ServerName deming.local
    ServerAdmin admin@example.com
    DocumentRoot /var/www/deming/public
    <Directory /var/www/deming>
    AllowOverride All
    </Directory>
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
    </VirtualHost>

Save and close the file when finished. Next, activate the Apache virtual host and rewrite module with the following commands:

    sudo a2enmod rewrite
    sudo a2dissite 000-default.conf
    sudo a2ensite deming.conf
    sudo a2dismod php8.1
    sudo a2enmod proxy_fcgi setenvif
    sudo a2enconf php8.1-fpm

Finally, restart the Apache service to activate the changes:

    sudo systemctl restart apache2

## PHP

You need to set the value of upload_max_filesize and post_max_size in your php.ini :

    ; Maximum allowed size for uploaded files.
    upload_max_filesize = 10M

    ; Must be greater than or equal to upload_max_filesize
    post_max_size = 10M

After modifying php.ini file(s), you need to restart your php-fpm service to use the new configuration.

    sudo systemctl restart php-fpm

## Mail configuration

If you wish to send notification e-mails from Deming.

Install postfix and mailx

    sudo apt install postfix mailutils

Configure postfix

    sudo dpkg-reconfigure postfix

Send a test mail with

    echo "Test mail body" | mailx -r "deming@yourdomain.local" -s "Subject Test" yourname@yourdomain.local

Don't forget to [configure](https://dbarzin.github.io/deming/config/#notifications) the content and frequency of your emails.

## Sheduler

Modify crontab

    sudo crontab -e

add this line to crontab

    * * * * * cd /var/www/deming && php artisan schedule:run >> /dev/null 2>&1

## Update

To update Deming, go to the Deming directory and retrieve the sources

    cd /var/www/deming
    git pull

Migrate database

    php artisan migrate

Update composer

    composer self-update

Update libraries

    composer update

Empty caches

    php artisan optimize:clear

## Reset to zero

To start from an empty database with the ISO 27001:2022 standard.

Here's the command to recreate the DB:

    php artisan migrate:fresh --seed

Then to populate the DB with 27001:2022

    php artisan db:seed --class=AttributeSeeder
    php artisan db:seed --class=DomainSeeder
    php artisan db:seed --class=MeasureSeeder
