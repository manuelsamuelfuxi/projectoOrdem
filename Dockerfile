# Usamos uma imagem oficial que já tem GD e MySQL pronto
FROM thecodingmachine/php:8.2-v4-fpm-apache AS builder

# Definir diretório de trabalho
WORKDIR /app

# Copiar ficheiros do projeto
COPY . /app

# Instalar dependências (Composer já vem instalado nesta imagem)
RUN composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-gd

# Permissões de pasta
RUN chown -R docker:docker /app \
    && chmod -R 775 /app/storage \
    && chmod -R 775 /app/bootstrap/cache

# Script de Start (Criado inline para não depender de ficheiros .sh)
RUN echo '#!/bin/bash\n\
php artisan migrate --force || true\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
php artisan storage:link || true\n\
apache2-foreground' > /usr/local/bin/start-app.sh && chmod +x /usr/local/bin/start-app.sh

CMD ["/usr/local/bin/start-app.sh"]