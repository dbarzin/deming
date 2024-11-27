# Procédure d'installation de Deming

## Configuration recommandée

- OS : Ubuntu 22.04 LTS
- RAM : 2G
- Disque : 30G
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

Créer les répertoires temporaires

    cd deming
    mkdir -p storage/framework/views
    mkdir -p storage/framework/cache
    mkdir -p storage/framework/sessions
    mkdir -p bootstrap/cache

Installer les packages avec composer

    composer install

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

    LANG=fr php artisan migrate --seed

Remarque: la graine est importante (--seed), car elle créera le premier utilisateur administrateur pour vous.

Générer la clé de l'application

    php artisan key:generate

Si vous voulez utiliser l'API, installez [Laravel Passport](https://laravel.com/docs/11.x/passport) (option) :

    php artisan passport:install

Créer le lien de stockage

	php artisan storage:link

## Peupler la base de données

Pour importer la base de données avec les attributs de sécurité de la norme 27001:2022 (optionel)

    LANG=fr php artisan db:seed --class=AttributeSeeder

Peupler la base de données avec la norme ISO 27001:2022 et générer un jeu de tests (optionel)

    php artisan deming:import-framework ./storage/app/repository/ISO27001-2022.fr.xlsx --clean
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

    sudo chown -R www-data:www-data /var/www/deming
    sudo chmod -R 775 /var/www/deming/storage

Ensuite, créez un nouveau fichier de configuration d'hôte virtuel Apache pour servir l'application :

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

Enregistrez et fermez le fichier lorsque vous avez terminé. Ensuite, activez l'hôte virtuel Apache et le module de réécriture avec les commandes suivantes :

    sudo a2enmod rewrite
    sudo a2dissite 000-default.conf
    sudo a2ensite deming.conf
    sudo a2dismod php8.3
    sudo a2enmod proxy_fcgi setenvif
    sudo a2enconf php8.3-fpm

Enfin, redémarrez le service Apache pour activer les modifications :

    sudo systemctl restart apache2

## PHP

Vous devez définir les valeurs de upload_max_filesize et post_max_size dans votre php.ini
(/etc/php/8.3/fpm/php.ini) :

    ; Taille maximale autorisée pour les fichiers téléchargés.
    upload_max_filesize = 10M

    ; Doit être supérieur ou égal à upload_max_filesize
    post_max_size = 10M

Après avoir modifié le(s) fichier(s) php.ini, vous devez redémarrer le service php-fpm  pour utiliser la nouvelle configuration.

    sudo systemctl restart php-fpm

## Configuration du mail

Si vous souhaitez envoyer des e-mails de notification depuis Deming.
Vous devez configurer l'accès au serveur SMTP dans .env

    MAIL_HOST='smtp.localhost'
    MAIL_PORT=2525
    MAIL_AUTH=true
    MAIL_SMTP_SECURE='ssl'
    MAIL_SMTP_AUTO_TLS=false
    MAIL_USERNAME=
    MAIL_PASSWORD=

Vous pouvez également configurer DKIM :

    MAIL_DKIM_DOMAIN = 'admin.local';
    MAIL_DKIM_PRIVATE = '/path/to/private/key';
    MAIL_DKIM_SELECTOR = 'default'; // Match your DKIM DNS selector
    MAIL_DKIM_PASSPHRASE = '';      // Only if your key has a passphrase

Don't forget to [configure](https://dbarzin.github.io/deming/config/#notifications) the content and frequency of your emails.

N'oubliez pas de [configurer](https://dbarzin.github.io/deming/config.fr/#notifications) le contenu et la fréquence d'envoi des mails.

## Configuration de Keycloak (optionnel)

Pour configurer Keycloak, suivez ces étapes :

- Ouvrez votre fichier .env.
- Modifiez les paramètres de configuration de Keycloak comme suit :

```bash
SOCIALITE_PROVIDERS="keycloak"
KEYCLOAK_CLIENT_ID= # Client Id (on Keycloak)
KEYCLOAK_CLIENT_SECRET=  # Client Secret
KEYCLOAK_REDIRECT_URI=${APP_URL}auth/callback/keycloak
KEYCLOAK_BASE_URL=<KeyCloak IP Address>
KEYCLOAK_REALM=   # Realm Name
```

Après avoir ajouter `keycloak` à la variable `SOCIALITE_PROVIDERS` un bouton apparaîtra sur la page de connexion, permettant aux utilisateurs de se connecter via Keycloak. (Il est possible de modifier le texte du bouton avec la variable `KEYCLAOK_DISPLAY_NAME`).

Pour autoriser la création d'utilisateur et/ou la mise à jour par Keycloak ajouter les paramètre suivants :

```bash
KEYCLOAK_ALLOW_CREATE_USER=true
KEYCLOAK_ALLOW_UPDATE_USER=true
```

Si vous souhaitez que récupérer le rôle de l'utilisateur fourni par Keycloak lors de sa création ou la mise à jour, il est nécessaire de lui de demander un `scope` supplémentaires et de définir le nom `claim` qui contiendra le rôle :
```bash
KEYCLOAK_ADDITIONAL_SCOPES="roles"
KEYCLOAK_ROLE_CLAIM="resource_access.deming.roles.0"
```

Il est également possible de fournir un rôle par défaut, utilisé si Keycloak ne fournit pas le rôle :
```bash
KEYCLOAK_DEFAULT_ROLE=<Valeur possible : auditee, auditor, user>
```

Pour une documentation plus complète sur la configuration de Keycloak, consultez la documentation officielle de Keycloak.

## Configuration d'un fournisseur OpenID Connect Générique

Il est possiblie d'ajouter un founisseur d'identité OpenID Connect générique, il suffit d'ajouter `oidc` à la variable `SOCIALITE_PROVIDERS`. Toutes les variables vu ci-dessus existe, elles commencent par `OIDC_` (voir le fichier .env.example pour plus d'information)

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

Mettre à jour les librairies

    composer install

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
