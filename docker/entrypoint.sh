#!/bin/sh
set -e

# Render inyecta la variable PORT (default 10000). Locally/Host: 80.
# Nginx debe escuchar en 0.0.0.0:${PORT} para que Render exponga el servicio.
sed -i "s/__PORT__/${PORT:-80}/g" /etc/nginx/http.d/default.conf

# Crear .env desde plantilla si no existe (el .env real nunca viaja al contenedor)
if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

# Generar APP_KEY si no existe
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null || [ -z "$(grep '^APP_KEY=' .env | cut -d'=' -f2-)" ]; then
    php artisan key:generate --force
fi

# Las variables del entorno del contenedor (DB_HOST, etc.) tienen prioridad sobre .env

# Limpiar y precachear config/views/rutas cuando existan
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Reintentar migraciones si la BD no está lista aún (Render + MySQL externo puede tardar)
for i in 1 2 3 4 5; do
    php artisan migrate --force 2>/dev/null && break
    echo "BD no disponible, reintento $i/5..."
    sleep 10
done

# Iniciar supervisord
exec "$@"