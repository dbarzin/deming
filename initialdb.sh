#!/bin/bash

# Vérifie si la variable d'environnement est égale à 1
if [ "${INITIAL_DB}" = "EN" ]; then
    # Se déplace vers le répertoire /var/www/deming/
    cd /var/www/deming/
    # Exécute la commande
    php artisan migrate --seed
    # Exit avec le code 0 pour indiquer que le script s'est terminé avec succès
    exit 0
fi
if [ "${INITIAL_DB}" = "FR" ]; then
    # Se déplace vers le répertoire /var/www/deming/
    cd /var/www/deming/
    # Exécute la commande
    LANG=fr php artisan migrate --seed
    # Exit avec le code 0 pour indiquer que le script s'est terminé avec succès
    exit 0
fi
