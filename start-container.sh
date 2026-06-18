#!/bin/bash

# 1. Rodar as migrations (se falhar, não aborta tudo, tenta continuar)
php artisan migrate --force --isolated=step || true

# 2. Limpar cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Link de storage (silencioso se já existir)
php artisan storage:link || true

# 4. Iniciar o PHP-FPM em primeiro plano
php-fpm -F