# Deming installation procedure

## Recommended configuration

- OS : Ubuntu 24.04.1 LTS
- RAM : 2G
- Disk : 30G
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

## Database

Install MariaDB (works also with ProgresSQL and MySQL)

    sudo apt install mariadb-server

Start database client

    sudo mariadb

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
    DB_CONNECTION=mariadb
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

If you want to use the API, install [Laravel Passport](https://laravel.com/docs/11.x/passport) (optional) :

    php artisan passport:install

Create storage link

    php artisan storage:link

Import attributes

    php artisan db:seed --class=AttributeSeeder

Then populate the database with 27001:2022 and generated tests data

    php artisan deming:import-framework ./storage/app/repository/ISO27001-2022.en.xlsx --clean
    php artisan deming:generate-tests

## Start application with PHP

    php artisan serve

or to access the application from another server

    php artisan serve --host 0.0.0.0 --port 8000

The application can be accessed at URL [http://127.0.0.1:8000]

    user : admin@admin.localhost
    password : admin

The administrator's default language is English. To change language, go to the user profile page
(top right of the main page).

To import a repository and generate test data, go to "Configuration" -> "Import" (optional).

## Start application with systemd

It is also possible to have the application start as a `systemd` service. For this you will need to create the service's defintion file:

	su root -c "vi /etc/systemd/system/deming.service"
 
and add it the following content:

	[Unit]
	Description=Deming
	After=network.target
	After=mariadb.service
	After=apache2.service
	
	[Service]
	Type=simple
	ExecStart=/usr/bin/php artisan serve --host 127.0.0.1 --port 8000
	User=www-data
	Group=www-data
	WorkingDirectory=/var/www/deming
	KillMode=mixed
	
	[Install]
	WantedBy=multi-user.target

Reload `systemd` to take this new service in account and start the application:

	systemctl daemon-reload
 	systemctl enable --now deming.service

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
    sudo a2dismod php8.3
    sudo a2enmod proxy_fcgi setenvif
    sudo a2enconf php8.3-fpm

Finally, restart the Apache service to activate the changes:

    sudo systemctl restart apache2

## Apache with HTTPS configuration

In addition to the above instructions, create or modify the virtual host configuration's file:

    su root -c "vi /etc/apache2/sites-available/deming.conf"

and add the following content:

	<VirtualHost *:80>
	   ServerName deming.local
	
	    RewriteEngine On
	    RewriteRule ^/?(.*) https://%{SERVER_NAME}/$1
	</VirtualHost>
	
	<VirtualHost *:443>
	    ServerName deming.local
	    DocumentRoot /var/www/deming/public
	
	    Protocols h2 h2c http/1.1
	
	    <Directory /var/www/deming>
	        AllowOverride All
	    </Directory>
	
	    ProxyPass / http://127.0.0.1:8000/
	    ProxyPassReverse / http://127.0.0.1:8000/
	    ProxyPreserveHost On

 	    # If you user php-fpm adapt the socket's path below and uncomment
	    #<FilesMatch \.php$>
	    #    SetHandler "proxy:unix:/var/run/php/php8.3-fpm.sock|fcgi://localhost/"
	    #</FilesMatch>
	
    	    ErrorLog ${APACHE_LOG_DIR}/error.log
    	    CustomLog ${APACHE_LOG_DIR}/access.log combined
	
	    <IfModule mod_headers.c>
	        Header always set Strict-Transport-Security "max-age=15552000; includeSubDomains; preload"
	        Header always set Referrer-Policy "no-referrer"
	        Header always set X-Content-Type-Options "no-sniff"
	        Header always set X-XSS-Protection "1; mode=block"
	        Header always set X-Robots-Tag "none"
	        Header always set X-Frame-Options "SAMEORIGIN"
	        Header edit Set-Cookie ^(.*)$ "$1;HttpOnly;Secure;SameSite=Strict"
	    </IfModule>
	
	    SSLEngine on
	    SSLCertificateFile  /etc/apache2/ssl/deming.local.crt
	    SSLCertificateKeyFile /etc/apache2/ssl/deming.local.key
	</VirtualHost>

## PHP

You need to set the value of upload_max_filesize and post_max_size in your php.ini
(/etc/php/8.3/fpm/php.ini) :

    ; Maximum allowed size for uploaded files.
    upload_max_filesize = 10M

    ; Must be greater than or equal to upload_max_filesize
    post_max_size = 10M

After modifying php.ini file(s), you need to restart your php-fpm service to use the new configuration.

    sudo systemctl restart php-fpm

## Mail configuration

If you wish to send notification e-mails from Deming.
You have to configure the SMTP server access in .env

    MAIL_HOST='smtp.localhost'
    MAIL_PORT=2525
    MAIL_AUTH=true
    MAIL_SMTP_SECURE='ssl'
    MAIL_SMTP_AUTO_TLS=false
    MAIL_USERNAME=
    MAIL_PASSWORD=

You may also configure DKIM :

    MAIL_DKIM_DOMAIN = 'admin.local';
    MAIL_DKIM_PRIVATE = '/path/to/private/key';
    MAIL_DKIM_SELECTOR = 'default'; // Match your DKIM DNS selector
    MAIL_DKIM_PASSPHRASE = '';      // Only if your key has a passphrase

Don't forget to [configure](https://dbarzin.github.io/deming/config/#notifications) the content and frequency of your emails.

## Keycloak Configuration (optional)

To configure [Keycloak](https://www.keycloak.org), follow these steps:

- Open your `.env` file.
- Modify the Keycloak configuration settings as follows:

```bash
SOCIALITE_PROVIDERS="keycloak"
KEYCLOAK_CLIENT_ID= # Client Id (on Keycloak)
KEYCLOAK_CLIENT_SECRET=  # Client Secret
KEYCLOAK_REDIRECT_URI=${APP_URL}auth/callback/keycloak
KEYCLOAK_BASE_URL=<KeyCloak IP Address>
KEYCLOAK_REALM=   # Realm Name
```

After adding `keycloak` to the `SOCIALITE_PROVIDERS` variable, a button will appear on the login page, allowing users to log in via Keycloak. (It is possible to modify the button text with the `KEYCLOAK_DISPLAY_NAME` variable).

To allow user creation and/or updates by Keycloak, add the following parameters:

```bash
KEYCLOAK_ALLOW_CREATE_USER=true
KEYCLOAK_ALLOW_UPDATE_USER=true
```

If you want to retrieve the user role provided by Keycloak during creation or update, it is necessary to request an additional `scope` and define the name of the `claim` that will contain the role:

```bash
KEYCLOAK_ADDITIONAL_SCOPES="roles"
KEYCLOAK_ROLE_CLAIM="resource_access.deming.roles.0"
```

It is also possible to provide a default role, used if Keycloak does not provide the role:

```bash
KEYCLOAK_DEFAULT_ROLE=<Possible value: auditee, auditor, user>
```

For more complete documentation on Keycloak configuration, consult the official Keycloak documentation.

## Configuration of a Generic OpenID Connect Provider

It is possible to add a generic OpenID Connect identity provider. Simply add `oidc` to the `SOCIALITE_PROVIDERS` variable. All the variables seen above exist, they start with `OIDC_` (see the `.env.example` file for more information).

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

Update libraries

    composer install

Empty caches

    php artisan optimize:clear

## Reset to zero

To start from an empty database with the ISO 27001:2022 standard.

Here's the command to recreate the DB:

    php artisan migrate:fresh --seed

Import attributes

    php artisan db:seed --class=AttributeSeeder

Then to populate the database with 27001:2022

    php artisan deming:import-framework ./storage/app/repository/ISO27001-2022.en.xlsx
