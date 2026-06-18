FROM composer:2 as build
WORKDIR /app
COPY . /app
RUN composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-gd

FROM php:8.2-fpm
# Instalar dependências do sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install gd pdo_mysql zip mbstring exif pcntl bcmath \
    && docker-php-ext-enable gd

# Copiar o composer do build anterior
COPY --from=build /app /app
WORKDIR /app

# Copiar o ficheiro de configuração padrão do PHP-FPM e configurar o caminho
RUN cp /usr/local/etc/php-fpm.d/www.conf.default /usr/local/etc/php-fpm.d/www.conf \
    && echo "clear_env = no" >> /usr/local/etc/php-fpm.d/www.conf

# Permissões de escrita (Storage)
RUN chown -R www-data:www-data /app \
    && chmod -R 775 /app/storage \
    && chmod -R 775 /app/bootstrap/cache

# Expor a porta 9000 (PHP-FPM) e 80 (O Railway espera a 80, mas vamos usar o script)
EXPOSE 9000

# Script de entrada para rodar migrations e iniciar o PHP-FPM
COPY start-container.sh /usr/local/bin/start-container.sh
RUN chmod +x /usr/local/bin/start-container.sh

CMD ["start-container.sh"]