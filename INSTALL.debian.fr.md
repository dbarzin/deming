# Procédure d'installation de Deming

## Configuration recommandée

- OS : Debian 12 stable
- RAM : 2G
- Disque : 30G
- VCPU 2

## Installation

Installer Debian :
- sans environement de bureau Debian
- avec serveur Web
- avec serveur SSH Web
- avec les utilitaires usuels du système

Mettre à jour la distribution

    su root -c "apt update"
    su root -c "apt upgrade"

Installer Apache, git, php et composer

    su root -c "apt-get install git composer apache2 libapache2-mod-php php php-mysql php-zip php-gd php-mbstring php-curl php-xml"

Créer le répertoire du projet

    cd /var/www
    su root -c "mkdir deming"
    su root -c "chown $USER:$GROUP deming"

Cloner le projet depuis Github dans /var/www

    cd /var/www
    git clone https://www.github.com/dbarzin/deming

Créer les répertoires temporaires

    cd deming
    mkdir -p storage/framework/views
    mkdir -p storage/framework/cache
    mkdir -p storage/framework/sessions
    mkdir -p bootstrap/cache


Installer les packages avec composer

    composer install

Publier tous les actifs publiables à partir des packages des fournisseurs

    php artisan vendor:publish --all

## MariaDB

Installer MariaDB

    su root -c "apt install mariadb-server"

Lancer MariaDB avec les droits root

    su root -c mariadb

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

    LANG=fr php artisan migrate --seed

Remarque: la graine est importante (--seed), car elle créera le premier utilisateur administrateur pour vous.

Générer la clé de l'application

    php artisan key:generate

Créer le lien de stockage

	php artisan storage:link

## Peupler la base de données

Pour importer la base de données avec les attributs de sécurité de la norme 27001:2022 (optionel)

    LANG=fr php artisan db:seed --class=AttributeSeeder

Peupler la base de données avec la norme ISO 27001:2022 et générer un jeu de tests (optionel)

    php artisan deming:import-framework ./storage/app/repository/ISO27001-2022.fr.xlsx
    php artisan deming:generate-tests

Démarrer l'application avec PHP

    php artisan serve

ou pour y accéder à l'application depuis un autre serveur

    php artisan serve --host 0.0.0.0 --port 8000

L'application est accessible à l'URL [http://127.0.0.1:8000]

    utilisateur : admin@admin.localhost
    mot de passe : admin

L'administrateur utilise la langue anglaise par défaut. Pour changer de langue, allez dans la page de profil de l'utilisateur
(en haut à droite de la page principale).

Pour importer un référentiel et générer des données de test, allez dans "Configuration" -> "Import" (optionel).

## Apache

Pour configurer Apache, modifiez les propriétés du répertoire Deming et accordez les autorisations appropriées au répertoire de stockage avec la commande suivante :

    su root -c "chown -R www-data:www-data /var/www/deming"
    su root -c "chmod -R 775 /var/www/deming/storage"

Ensuite, créez un nouveau fichier de configuration d'hôte virtuel Apache pour servir l'application :

    su root -c "vi /etc/apache2/sites-available/deming.conf"

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

Enregistrez et fermez le fichier lorsque vous avez terminé. Ensuite, activez l'hôte virtuel Apache et le module de réécriture avec les commandes suivantes :

    su - root -c "a2enmod rewrite"
    su - root -c "a2dissite 000-default.conf"
    su - root -c "a2ensite deming.conf"

Enfin, redémarrez le service Apache pour activer les modifications :

    su - root -c "systemctl restart apache2"

## PHP

Vous devez définir les valeurs de upload_max_filesize et post_max_size dans votre php.ini (/etc/php/8.2/apache2/php.ini):

    ; Taille maximale autorisée pour les fichiers téléchargés.
    upload_max_filesize = 10M

    ; Doit être supérieur ou égal à upload_max_filesize
    post_max_size = 10M

Après avoir modifié le(s) fichier(s) php.ini, vous devez redémarrer le service Apache pour utiliser la nouvelle configuration.

    su - root -c "systemctl restart apache2"

## Configuration du mail

Si vous souhaitez envoyer des e-mails de notification depuis Deming.
Vous devez configurer l'accès au serveur SMTP dans .env

    MAIL_HOST='smtp.localhost'
    MAIL_PORT=2525
    MAIL_AUTH=true
    MAIL_SMTP_SECURE='ssl'
    MAIL_USERNAME=
    MAIL_PASSWORD=

Vous pouvez également configurer DKIM :

    MAIL_DKIM_DOMAIN = 'admin.local';
    MAIL_DKIM_PRIVATE = '/path/to/private/key';
    MAIL_DKIM_SELECTOR = 'default'; // Match your DKIM DNS selector
    MAIL_DKIM_PASSPHRASE = '';      // Only if your key has a passphrase

N'oubliez pas de [configurer](https://dbarzin.github.io/deming/config.fr/#notifications) le contenu et la fréquence d'envoi des mails.

## Sheduler

Modifier le crontab

    sudo crontab -e

ajouter cette ligne dans le crontab

    * * * * * cd /var/www/deming && php artisan schedule:run >> /dev/null 2>&1

## Mise à jour

Pour mettre à jour Deming, il faut aller dans le répoertoire de Deming et récupérer les sources

    cd /var/www/deming
    git pull

Migrer la base de données

    php artisan migrate

Mettre à jour composer

    composer self-update

Mettre à jour les librairies

    composer update

Vider les caches

    php artisan optimize:clear

## Remise à zéro

Pour repartir d'une base de données vide avec la norme ISO 27001:2022.

Voici la commande pour recréer la DB :

    php artisan migrate:fresh --seed

Puis importer les attributs

    php artisan db:seed --class=AttributeSeeder

Peupler la base de données avec la norme ISO 27001:2022

    php artisan deming:import-framework ./storage/app/repository/ISO27001-2022.fr.xlsx
