#!/bin/bash

# Gerar APP_KEY se não existir
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Migrações (opcional — comenta se não quiseres correr automaticamente)
php artisan migrate --force

# Limpar e recriar cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Arrancar php-fpm em background
php-fpm &

# Arrancar nginx em foreground
nginx -g "daemon off;"