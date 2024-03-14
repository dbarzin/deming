#!/bin/bash

# Vérifie si la variable d'environnement est égale à 1
if [ "${RESET_DB}" = "EN" ]; then
    # Se déplace vers le répertoire /var/www/deming/
    cd /var/www/deming/
    # Exécute la commande
    php artisan migrate:fresh --seed
    # Exit avec le code 0 pour indiquer que le script s'est terminé avec succès
    exit 0
fi
if [ "${RESET_DB}" = "FR" ]; then
    # Se déplace vers le répertoire /var/www/deming/
    cd /var/www/deming/
    # Exécute la commande
    LANG=fr php artisan migrate:fresh --seed
    # Exit avec le code 0 pour indiquer que le script s'est terminé avec succès
    exit 0
fi
