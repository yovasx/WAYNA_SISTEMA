#!/bin/bash
# ===========================================================
#  WAYNA — Entrypoint PHP
#  Se ejecuta cada vez que el contenedor arranca.
#  Hace todo automáticamente para que el equipo solo
#  necesite: docker compose up -d
# ===========================================================
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

# ── 3. Instalar dependencias Composer ─────────────────────
if [ ! -f "vendor/autoload.php" ]; then
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
    echo "▶ Copiando .env.example .env"
    cp .env.example .env
fi

# ── 5. Generar APP_KEY si está vacía ──────────────────────
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=\"\"" .env; then
    echo "▶ Generando APP_KEY..."
    php artisan key:generate --force || echo "  ⚠ APP_KEY fallo"
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
    php artisan storage:link 2>&1 || echo "  ⚠ Storage link fallo"
fi

# ── 8. Limpiar cache de config (evita valores viejos) ─────
echo "▶ Limpiando cache de configuracion..."
php artisan config:clear 2>&1 || echo "  ⚠ Config clear fallo (puede ser normal)"
echo "  ✓ Config cache limpia"

# ── 9. Migraciones ────────────────────────────────────────
echo "▶ Ejecutando migraciones..."
php artisan migrate --force 2>&1 || echo "  ⚠ Migraciones fallaron"
echo "  ✓ Migraciones completadas"

# ── 10. Seed (solo primera vez, datos demo) ───────────────
if [ ! -f "storage/app/.seeded" ]; then
    echo "▶ Sembrando datos demo iniciales..."
    php artisan db:seed --force 2>&1 || echo "  ⚠ Seed fallo (puede ser normal si ya hay datos)"
    touch storage/app/.seeded
    echo "  ✓ Seed completado"
fi

# ── 11. Cache según entorno ────────────────────────────────
if [ "$APP_ENV" = "production" ]; then
    echo "▶ Cacheando configuracion (produccion)..."
    php artisan config:cache 2>&1 || echo "  ⚠ Config cache fallo"
    php artisan route:cache 2>&1 || echo "  ⚠ Route cache fallo"
    php artisan view:cache 2>&1 || echo "  ⚠ View cache fallo"
    echo "  ✓ Cache de produccion OK"
else
    echo "  ✓ Entorno local: cache omitida"
fi

echo ""
echo "╔══════════════════════════════════════╗"
echo "║     WAYNA lista en puerto 9090       ║"
echo "╚══════════════════════════════════════╝"
echo ""

exec "$@"
