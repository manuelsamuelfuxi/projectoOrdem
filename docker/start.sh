#!/bin/bash

set -e

echo "==> A iniciar container AATDSPA..."

# ── APP_KEY ───────────────────────────────────────────────────────────────────
if [ -z "$APP_KEY" ]; then
    echo "==> Sem APP_KEY — a gerar..."
    php artisan key:generate --force
fi

# ── Permissões de storage ─────────────────────────────────────────────────────
echo "==> A configurar permissões..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# ── Migrações ─────────────────────────────────────────────────────────────────
echo "==> A correr migrações..."
php artisan migrate --force

# ── Storage link ──────────────────────────────────────────────────────────────
echo "==> A criar link simbólico do storage..."
php artisan storage:link || true

# ── Cache ─────────────────────────────────────────────────────────────────────
echo "==> A limpar cache antiga..."
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear

echo "==> A recriar cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── Configuração PHP — limites de upload ──────────────────────────────────────
echo "==> A configurar PHP..."
cat > /usr/local/etc/php/conf.d/uploads.ini << 'EOF'
upload_max_filesize = 20M
post_max_size       = 25M
memory_limit        = 256M
max_execution_time  = 120
max_input_time      = 120
EOF

# ── PHP-FPM em background ─────────────────────────────────────────────────────
echo "==> A arrancar PHP-FPM..."
php-fpm -D

# Aguardar PHP-FPM estar pronto
sleep 2

# ── Nginx em foreground ───────────────────────────────────────────────────────
echo "==> A arrancar Nginx..."
exec nginx -g "daemon off;"