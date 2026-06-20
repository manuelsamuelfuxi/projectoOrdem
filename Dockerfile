FROM php:8.2-fpm

# ── Dependências do sistema + Node.js ─────────────────────────────────────────
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip nginx \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ── Composer ──────────────────────────────────────────────────────────────────
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ── Configuração PHP — limites de upload e memória ───────────────────────────
RUN echo "upload_max_filesize = 20M"  >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size = 25M"        >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "memory_limit = 256M"        >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "max_execution_time = 120"   >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "max_input_time = 120"       >> /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www

# ── Copiar e instalar dependências (cache de layers) ──────────────────────────
COPY package*.json ./
RUN npm install

COPY composer*.json ./
RUN composer install --optimize-autoloader --no-dev --no-scripts

# ── Copiar restante do projecto ───────────────────────────────────────────────
COPY . .

# ── Compilar assets ───────────────────────────────────────────────────────────
RUN npm run build

# ── Scripts pós-install do Composer ──────────────────────────────────────────
RUN composer run-script post-autoload-dump || true

# ── Permissões ────────────────────────────────────────────────────────────────
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# ── Nginx ─────────────────────────────────────────────────────────────────────
COPY docker/nginx.conf /etc/nginx/sites-available/default

EXPOSE 80

# ── Script de arranque ────────────────────────────────────────────────────────
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]