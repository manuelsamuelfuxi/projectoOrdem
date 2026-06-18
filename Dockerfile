FROM composer:2 as build
WORKDIR /app
COPY . /app
RUN composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-gd

FROM php:8.2-fpm

# Instalar dependências e extensões (GD e MySQL)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install gd pdo_mysql zip mbstring exif pcntl bcmath

# Copiar vendor e app
COPY --from=build /app /app
WORKDIR /app

# Configurar PHP-FPM para não limpar variáveis de env
RUN cp /usr/local/etc/php-fpm.d/www.conf.default /usr/local/etc/php-fpm.d/www.conf \
    && echo "clear_env = no" >> /usr/local/etc/php-fpm.d/www.conf

# Permissões
RUN chown -R www-data:www-data /app \
    && chmod -R 775 /app/storage \
    && chmod -R 775 /app/bootstrap/cache

# Criar um script de start inline (sem precisar de ficheiro .sh externo)
RUN echo '#!/bin/bash\n\
php artisan migrate --force --isolated=step || true\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
php artisan storage:link || true\n\
php-fpm -F -R' > /usr/local/bin/start-app.sh && chmod +x /usr/local/bin/start-app.sh

CMD ["/usr/local/bin/start-app.sh"]