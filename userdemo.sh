#!/bin/bash

# Vérifie si la variable d'environnement USE_DEMO_DATA est égale à 1
if [ "${USE_DEMO_DATA}" = "1" ]; then
    # Se déplace vers le répertoire /var/www/deming/
    cd /var/www/deming/
    # seed atabase
    php artisan db:seed --class=AttributeSeeder
    # Import framework
    php artisan deming:import-framework ./storage/app/repository/ISO27001-2022.en.xlsx --clean
    # Exécute la commande "php artisan deming:generateTests"
    php artisan deming:generate-tests
    # Exit avec le code 0 pour indiquer que le script s'est terminé avec succès
    exit 0
fi
