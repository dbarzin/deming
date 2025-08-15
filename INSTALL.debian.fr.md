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

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=deming
DB_USERNAME=deming_user
DB_PASSWORD=demPasssword-123
```

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

## Démarrer l'application avec PHP

    php artisan serve

ou pour y accéder à l'application depuis un autre serveur

    php artisan serve --host 0.0.0.0 --port 8000

L'application est accessible à l'URL [http://127.0.0.1:8000]

    utilisateur : admin@admin.localhost
    mot de passe : admin

L'administrateur utilise la langue anglaise par défaut. Pour changer de langue, allez dans la page de profil de l'utilisateur
(en haut à droite de la page principale).

Pour importer un référentiel et générer des données de test, allez dans "Configuration" -> "Import" (optionel).

## Démarrer l'application avec systemd

Il est également possible de faire démarrer l'application en tant que service `systemd`. Pour cela, créez un nouveau fichier de définition du service :

	su root -c "vi /etc/systemd/system/deming.service"

Ajoutez les lignes suivantes:

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

Prenez en compte ce nouveau service et démarrez l'application :

	systemctl daemon-reload
 	systemctl enable --now deming.service

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

## Apache avec configuration HTTPS

En complément des étapes ci-dessus, créez ou modifiez le fichier de configuration d'hôte virtuel Apache pour servir l'application :

    su root -c "vi /etc/apache2/sites-available/deming.conf"

Ajouter les lignes suivantes :

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

 	    # Si vous utilisez php-fpm (chemin de la socket à adapter à votre cas)
	    #<FilesMatch \.php$>
	    #    SetHandler "proxy:unix:/var/run/php/php8.2-fpm.sock|fcgi://localhost/"
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

Dans le cadre de cette configuration servant l'application en HTTPS, il pourra être nécessaire de positionner la variable `APP_ENV` à la valeur `production` dans le fichier `.env` de Deming.

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

```dotenv
MAIL_HOST='smtp.localhost'
MAIL_PORT=2525
MAIL_AUTH=true
MAIL_SMTP_SECURE='ssl'
MAIL_SMTP_AUTO_TLS=false
MAIL_USERNAME=
MAIL_PASSWORD=
```

Vous pouvez également configurer DKIM :

```dotenv
MAIL_DKIM_DOMAIN = 'admin.local';
MAIL_DKIM_PRIVATE = '/path/to/private/key';
MAIL_DKIM_SELECTOR = 'default'; // Match your DKIM DNS selector
MAIL_DKIM_PASSPHRASE = '';      // Only if your key has a passphrase
```

N'oubliez pas de [configurer](https://dbarzin.github.io/deming/config.fr/#notifications) le contenu et la fréquence d'envoi des mails.

## LDAP / LDAPRecord configuration (optional)

Cette section te permet d’activer l’authentification LDAP dans Deming avec **LDAPRecord v2**. Elle fonctionne indifféremment avec **Active Directory** *ou* **OpenLDAP**, et peut cohabiter avec l’authentification locale (base de données).

### Prérequis

Prérequis : l’extension PHP LDAP doit être installée et active.

```bash
sudo apt-get install php-ldap
sudo systemctl restart apache2
```

### Environnement

Ajouter / adapter les variables suivantes :

```dotenv
# Activation LDAP dans Deming (mode hybride)
LDAP_ENABLED=true                 # Active l’authentification LDAP
LDAP_FALLBACK_LOCAL=true          # Si LDAP échoue, tenter l’auth locale
LDAP_AUTO_PROVISION=false         # Créer automatiquement l’utilisateur local après bind LDAP OK

# Connexion au serveur LDAP
LDAP_HOST=ldap.example.org
LDAP_PORT=389                     # 389 (StartTLS) ou 636 (LDAPS)
LDAP_BASE_DN=dc=example,dc=org
LDAP_USERNAME=cn=admin,dc=example,dc=org   # Compte de service pour la recherche
LDAP_PASSWORD=********
LDAP_TLS=true                     # StartTLS (recommandé si port 389)
LDAP_SSL=false                    # true si tu utilises ldaps:// sur 636
LDAP_TIMEOUT=5                    # (optionnel)

# Attributs candidats pour identifier l’utilisateur saisi dans le formulaire
# L’ordre a de l’importance : le premier qui matche est utilisé.
# OpenLDAP: uid, cn, mail ; AD: sAMAccountName, userPrincipalName, mail
LDAP_LOGIN_ATTRIBUTES=uid,cn,mail,sAMAccountName,userPrincipalName
```

**Exemples**

* OpenLDAP (DN utilisateur typique : `uid=jdupont,ou=people,dc=example,dc=org`) :

  ```dotenv
  LDAP_TLS=true
  LDAP_SSL=false
  LDAP_LOGIN_ATTRIBUTES=uid,cn,mail
  ```
* Active Directory (UPN : `jdupont@example.org`, sAM : `jdupont`) :

  ```dotenv
  LDAP_TLS=true
  LDAP_SSL=false
  LDAP_LOGIN_ATTRIBUTES=sAMAccountName,userPrincipalName,mail,cn
  LDAP_USERNAME=EXAMPLE\\svc_ldap   # ou DN complet du compte de service
  ```

Après modification du `.env` :

```bash
php artisan config:clear
php artisan optimize:clear
```

### Certificats

**Certificats** : si tu utilises StartTLS/LDAPS avec un CA interne, ajoute le CA au trust store de ta distribution (ex. `/usr/local/share/ca-certificates` + `update-ca-certificates`).

### Comment ça marche côté Deming

Le contrôleur d’authentification :

* recherche l’entrée LDAP via une **OR**-query sur les attributs listés dans `LDAP_LOGIN_ATTRIBUTES` ;
* récupère le **DN** ;
* tente un **bind** avec le mot de passe saisi ;
* si OK, il connecte l’utilisateur applicatif local correspondant (et peut **auto-provisionner** s’il n’existe pas, selon `LDAP_AUTO_PROVISION`).

### Test rapide

Avant de tester via l’UI, valide la connexion en CLI :

```bash
php artisan tinker
```

Puis dans Tinker :

```php
use LdapRecord\Container;

$dn = 'uid=jdupont,ou=people,dc=example,dc=org';  // ou cn=..., ou DN AD
Container::getConnection()->auth()->attempt($dn, 'MOT_DE_PASSE', true);
// => true attendu si l’authentification réussit
```

### Dépannage express

* **PHP LDAP manquant** : `php -m | grep ldap` doit renvoyer `ldap`. Sinon `sudo apt-get install php-ldap`, puis redémarrage PHP/Apache.
* **Mauvais DN** : ajoute les `ou=...` corrects (ex. `ou=people`).
* **TLS/SSL** : StartTLS = `LDAP_TLS=true` (389). LDAPS = `LDAP_SSL=true` (636). Ne laisse pas en clair en prod.
* **Certificat non approuvé** : ajoute le CA au système (voir ci-dessus).
* **Recherche impossible** : vérifie `LDAP_USERNAME` / `LDAP_PASSWORD` (compte de service) et les ACL de l’annuaire.
* **Attribut de login** : aligne `LDAP_LOGIN_ATTRIBUTES` avec ce que tes utilisateurs saisissent réellement (uid ? UPN ? email ?).

### Sécurité – à faire, pas à discuter

* Utilise **TLS/LDAPS**. Point.
* Restreins les droits du **compte de service** au strict nécessaire (lecture des attributs requis).
* Évite de logger des mots de passe ou DN complets côté application.
* Si tu actives l’**auto-provisioning**, stocke un mot de passe aléatoire côté base (pas un duplicat du LDAP) et applique tes politiques de rôles/profils.


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
