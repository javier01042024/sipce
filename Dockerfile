# ============================================================
# SIPCE - Dockerfile (multi-etapa)
# Laravel 12 + PHP 8.2 + MySQL + vite
# ============================================================

# ---------- ETAPA 1: Dependencias PHP (Composer) ----------
FROM composer:2 AS vendor
WORKDIR /sipce

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-req=ext-gd \
    && composer dump-autoload --no-dev --no-scripts --ignore-platform-req=ext-gd

# ---------- ETAPA 2: Assets frontend (Node + Vite) ----------
FROM node:22 AS frontend
WORKDIR /sipce

COPY package.json package-lock.json* ./
RUN npm install --no-audit --no-fund
COPY . .
RUN npm run build

# ---------- ETAPA 3: Imagen final (runtime) ----------
FROM php:8.2-fpm-alpine

# Extensiones necesarias: pdo, pdo_mysql, pdo_pgsql, pdo_sqlite, gd,
# zip, mbstring, intl, bcmath, opcache, exif, pcntl
RUN apk add --no-cache \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        oniguruma-dev \
        icu-dev \
        libxml2-dev \
        sqlite-dev \
        zip \
        unzip \
        git \
        curl \
        nginx \
        supervisor \
        postgresql-client \
        postgresql-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        pdo_sqlite \
        gd \
        zip \
        mbstring \
        intl \
        bcmath \
        opcache \
        exif \
        pcntl \
    && docker-php-ext-enable opcache \
    && apk del postgresql-dev

# Composer (binario entregado por la etapa vendor)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar código fuente
COPY . .

# Copiar dependencias PHP compiladas
COPY --from=vendor /sipce/vendor /var/www/html/vendor

# Copiar assets frontend compilados
COPY --from=frontend /sipce/public/build /var/www/html/public/build

# Generar el manifest de auto-discovery de paquetes (necesita vendor + código)
RUN php artisan package:discover --ansi

# php-fpm: heredar las variables de entorno del contenedor (usadas por Render)
RUN echo 'clear_env = no' >> /usr/local/etc/php-fpm.d/www.conf

# Configuración PHP (opcache, límites)
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Usuario que usará PHP-FPM y Nginx
RUN mkdir -p storage/framework/cache/data \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && find /var/www/html/storage -type d -exec chmod 775 {} \; \
    && find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \;

# Nginx conf (plantilla con __PORT__ reemplazada en el entrypoint:
# Render inyecta la variable PORT y el contenedor debe escuchar en 0.0.0.0:$PORT)
COPY docker/nginx/default.conf.tpl /etc/nginx/http.d/default.conf

# Supervisor: corre PHP-FPM + Nginx juntos
RUN printf '[supervisord]\nnodaemon=true\nuser=root\n\n[program:php-fpm]\ncommand=php-fpm\nstdout_logfile=/dev/stdout\nstdout_logfile_maxbytes=0\nstderr_logfile=/dev/stderr\nstderr_logfile_maxbytes=0\n\n[program:nginx]\ncommand=nginx -g "daemon off;"\nstdout_logfile=/dev/stdout\nstdout_logfile_maxbytes=0\nstderr_logfile=/dev/stderr\nstderr_logfile_maxbytes=0\n' > /etc/supervisord.conf

# Permisos finales
RUN chown -R www-data:www-data /var/www/html/vendor /var/www/html/public/build 2>/dev/null || true

EXPOSE 80

# Entrypoint: genera clave si falta, migra, y arranca supervisor
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]