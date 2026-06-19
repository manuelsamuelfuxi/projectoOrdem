#!/bin/bash

# Gerar APP_KEY se não existir
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Migrações
php artisan migrate --force

# Link simbólico para o storage
php artisan storage:link

# Limpar e recriar cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Arrancar php-fpm em background
php-fpm &

# Arrancar nginx em foreground
nginx -g "daemon off;"