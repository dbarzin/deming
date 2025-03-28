#!/bin/bash

# Vérifie si la variable d'environnement est égale à 1
if [ "${UPLOAD_DB_ISO27001}" = "EN" ]; then
    # Se déplace vers le répertoire /var/www/deming/
    cd /var/www/deming/
    # Exécute la commande
    php artisan db:seed --class=AttributeSeeder 
    php artisan db:seed --class=DomainSeeder 
    php artisan db:seed --class=MeasureSeeder
    # Exit avec le code 0 pour indiquer que le script s'est terminé avec succès
    exit 0
fi
if [ "${UPLOAD_DB_ISO27001}" = "FR" ]; then
    # Se déplace vers le répertoire /var/www/deming/
    cd /var/www/deming/
    # Exécute la commande
    LANG=fr php artisan db:seed --class=AttributeSeeder
    LANG=fr php artisan db:seed --class=DomainSeeder
    LANG=fr php artisan db:seed --class=MeasureSeeder
    # Exit avec le code 0 pour indiquer que le script s'est terminé avec succès
    exit 0
fi
if [ "${UPLOAD_DB_ISO27001}" = "DE" ]; then
    # Go to directory /var/www/deming/
    cd /var/www/deming/
    # Executes the command
    LANG=de php artisan db:seed --class=AttributeSeeder
    LANG=de php artisan db:seed --class=DomainSeeder
    LANG=de php artisan db:seed --class=MeasureSeeder
    # Exit with code 0 to indicate that the script has ended successfully
    exit 0
fi
