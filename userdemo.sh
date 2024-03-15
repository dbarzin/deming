#!/bin/bash

# Vérifie si la variable d'environnement USE_DEMO_DATA est égale à 1
if [ "${USE_DEMO_DATA}" = "1" ]; then
    # Se déplace vers le répertoire /var/www/deming/
    cd /var/www/deming/
    # Exécute la commande "php artisan deming:generateTests"
    php artisan deming:generateTests
    # Exit avec le code 0 pour indiquer que le script s'est terminé avec succès
    exit 0
fi
