#!/usr/bin/env bash
set -e

cd /var/www/deming

DB_H="mysql"

echo "Waiting for MySQL to be ready..."
until mysqladmin ping -h"${DB_H}" --silent 2>/dev/null; do
    echo "  Not ready, retrying in 3s..."
    sleep 3
done
echo "MySQL is ready."

# APP_KEY — générer seulement si absent
grep -q '^APP_KEY=base64:' .env || php artisan key:generate --no-interaction

# Initialisation DB
bash /etc/resetdb.sh
bash /etc/initialdb.sh

# Storage
php artisan storage:link --quiet

# Import référentiel et données de démo
bash /etc/uploadiso27001db.sh || echo "uploadiso27001db skipped"
bash /etc/userdemo.sh || echo "userdemo skipped"

# Passport (OAuth)
php artisan passport:install --force || echo "Passport skipped"
if ls storage/oauth-*.key 2>/dev/null; then
    chown www-data:www-data storage/oauth-*.key
    chmod 600 storage/oauth-*.key
fi

# Services système
service cron start || true

# Copier le vhost nginx
rm -f /etc/nginx/sites-enabled/default

# Démarrer artisan serve en arrière-plan (port 8000 — cible du reverse proxy nginx)
php artisan serve --host 0.0.0.0 --port 8000 &

# Nginx en PID 1
exec nginx -g "daemon off;"
