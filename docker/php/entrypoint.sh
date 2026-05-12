#!/bin/bash
# ============================================================
#  WAYNA — Entrypoint PHP
#  Se ejecuta cada vez que el contenedor arranca.
#  Hace todo automáticamente para que el equipo solo
#  necesite: docker compose up -d
# ============================================================
set -e

echo ""
echo "╔══════════════════════════════════════╗"
echo "║         WAYNA — Iniciando...         ║"
echo "╚══════════════════════════════════════╝"
echo ""

# ── 1. Esperar PostgreSQL ─────────────────────────────────
echo "▶ Esperando PostgreSQL..."
until php -r "
    try {
        new PDO(
            'pgsql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
        exit(0);
    } catch (Exception \$e) {
        exit(1);
    }
" 2>/dev/null; do
    echo "  · Postgres no disponible, reintentando en 3s..."
    sleep 3
done
echo "  ✓ PostgreSQL listo"

# ── 2. Esperar Redis ──────────────────────────────────────
echo "▶ Esperando Redis..."
until php -r "
    \$redis = new Redis();
    try {
        \$redis->connect(getenv('REDIS_HOST'), 6379, 3);
        \$pass = getenv('REDIS_PASSWORD');
        if (\$pass) \$redis->auth(\$pass);
        exit(0);
    } catch (Exception \$e) {
        exit(1);
    }
" 2>/dev/null; do
    echo "  · Redis no disponible, reintentando en 3s..."
    sleep 3
done
echo "  ✓ Redis listo"

# ── 3. Crear proyecto o instalar dependencias ────────────────
if [ ! -f "composer.json" ]; then
    echo "▶ Inicializando proyecto Laravel por primera vez..."
    composer create-project --prefer-dist laravel/laravel temp_app
    cp -Rn temp_app/* .
    cp -Rn temp_app/.* . 2>/dev/null || true
    rm -rf temp_app
    echo "  ✓ Proyecto Laravel inicializado"
elif [ ! -f "vendor/autoload.php" ]; then
    echo "▶ Instalando dependencias Composer (primera vez)..."
    composer install \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader \
        --no-progress
    echo "  ✓ Composer listo"
else
    echo "  ✓ Vendor ya existe, omitiendo composer install"
fi

# ── 4. Copiar .env si no existe ───────────────────────────
if [ ! -f ".env" ]; then
    echo "▶ Copiando .env.example → .env"
    cp .env.example .env
fi

# ── 5. Generar APP_KEY si está vacía ──────────────────────
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=\"\"" .env; then
    echo "▶ Generando APP_KEY..."
    php artisan key:generate --force
    echo "  ✓ APP_KEY generada"
fi

# ── 6. Permisos de storage ────────────────────────────────
echo "▶ Ajustando permisos..."
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
echo "  ✓ Permisos OK"

# ── 7. Storage link ───────────────────────────────────────
if [ ! -L "public/storage" ]; then
    echo "▶ Creando storage:link..."
    php artisan storage:link
fi

# ── 8. Migraciones ────────────────────────────────────────
echo "▶ Ejecutando migraciones..."
php artisan migrate --force
echo "  ✓ Migraciones OK"

# ── 9. Cache según entorno ────────────────────────────────
if [ "$APP_ENV" = "production" ]; then
    echo "▶ Cacheando configuración (producción)..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    echo "  ✓ Cache de producción OK"
else
    echo "  ✓ Entorno local: cache omitida"
fi

echo ""
echo "╔══════════════════════════════════════╗"
echo "║     WAYNA lista en puerto 9090       ║"
echo "╚══════════════════════════════════════╝"
echo ""

exec php-fpm
