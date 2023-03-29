# Procédure d'installation de Deming

## Configuration recommandée

- OS : Ubuntu 21.10
- RAM : 2G
- Disque : 120G
- VCPU 2

## Installation 

Mettre à jour la distribution linux

    sudo apt update && sudo apt upgrade

Installer Apache, git, php et composer

    sudo apt-get install git composer apache2 libapache2-mod-php php php-cli php-opcache php-mysql php-zip php-gd php-mbstring php-curl php-xml -y

Créer le répertoire du projet

    cd /var/www
    sudo mkdir deming
    sudo chown $USER:$GROUP deming

Cloner le projet depuis Github

    git clone https://www.github.com/dbarzin/deming

Installer les packages avec composer :

    cd deming
    mkdir -p storage/framework/views
    mkdir -p storage/framework/cache
    mkdir -p storage/framework/sessions
    mkdir -p bootstrap/cache
    composer install

Publier tous les actifs publiables à partir des packages des fournisseurs

    php artisan vendor:publish --all

## MySQL

Installer MySQL

    sudo apt install mysql-server

Vérifier que vous utilisez MySQL et pas MariaDB (Deming ne fonctionne pas avec MariaDB).

    sudo mysql --version

Lancer MySQL avec les droits root

    sudo mysql

Créer la base de données _deming_ et l'utilisateur _deming_user_

    CREATE DATABASE deming CHARACTER SET utf8 COLLATE utf8_general_ci;
	CREATE USER 'deming_user'@'localhost' IDENTIFIED BY 'demPasssword-123';
    GRANT ALL ON deming.* TO deming_user@localhost;
    GRANT PROCESS ON *.* TO 'deming_user'@'localhost';

    FLUSH PRIVILEGES;
    EXIT;

## Configuration

Créer un fichier .env dans le répertoire racine du projet :

    cd /var/www/deming
    cp .env.example .env

Mettre les paramètre de connexion à la base de données :

    vi .env

    ## .env file
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
	DB_DATABASE=deming
	DB_USERNAME=deming_user
	DB_PASSWORD=demPasssword-123

## Créer la base de données

Exécuter les migrations

    php artisan migrate --seed

Remarque: la graine est importante (--seed), car elle créera le premier utilisateur administrateur pour vous.

Générer la clé de l'application

    php artisan key:generate

Créer le lien de stockage

	php artisan storage:link

Pour importer la base de données avec les mesures de sécurité de la norme 27002:2013

    sudo mysql deming < deming-27k2\:2013.sql

Génrérer des données de test (optionnel)

    php artisan deming:generateTests

Démarrer l'application avec php

    php artisan serve

ou pour y accéder à l'application depuis un autre serveur

    php artisan serve --host 0.0.0.0 --port 8000

L'application est accessible à l'URL [http://127.0.0.1:8000]

    utilisateur : admin@admin.localhost
    mot de passe : admin

## Apache

Pour configurer Apache, modifiez les propriétés du répertoire deming et accordez les autorisations appropriées au répertoire de stockage avec la commande suivante

    sudo chown -R www-data:www-data /var/www/deming
    sudo chmod -R 775 /var/www/deming/storage

Ensuite, créez un nouveau fichier de configuration d'hôte virtuel Apache pour servir l'application Mercator :

    sudo vi /etc/apache2/sites-available/deming.conf

Ajouter les lignes suivantes :

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

Enregistrez et fermez le fichier lorsque vous avez terminé. Ensuite, activez l'hôte virtuel Apache et le module de réécriture avec la commande suivante :

    sudo a2enmod rewrite
    sudo a2dissite 000-default.conf
    sudo a2ensite deming.conf

Enfin, redémarrez le service Apache pour activer les modifications :

    sudo systemctl restart apache2
