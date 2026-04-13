#!/bin/bash

# Définit un délai de sommeil par défaut de 10 secondes
DEFAULT_SLEEP=1

# Vérifie si la variable d'environnement RESET_DB_SLEEP est définie
if [ -n "${DB_SLEEP}" ]; then
    # Utilise la valeur définie par l'utilisateur
    SLEEP_TIME="${DB_SLEEP}"
else
    # Utilise la valeur par défaut
    SLEEP_TIME="${DEFAULT_SLEEP}"
fi

# Affiche le message
echo "Waiting for ${SLEEP_TIME} seconds before executing migration..."
# Attend le nombre de secondes spécifié
sleep "${SLEEP_TIME}"

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
