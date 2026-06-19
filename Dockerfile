FROM php:8.2-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip nginx \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copiar ficheiros do projeto
COPY . .

# Instalar dependências PHP
RUN composer install --optimize-autoloader --no-dev

# Permissões
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Copiar configuração do Nginx
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Gerar key se não existir
RUN php artisan config:cache || true

EXPOSE 80

# Script de arranque
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]