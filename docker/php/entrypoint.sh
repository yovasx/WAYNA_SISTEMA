#!/bin/bash
# ===========================================================
#  WAYNA — Entrypoint PHP
#  Se ejecuta cada vez que el contenedor arranca.
#  Hace todo automáticamente para que el equipo solo
#  necesite: docker compose up -d
# ===========================================================

echo ""
echo "╔══════════════════════════════════════╗"
echo "║         WAYNA — Iniciando...         ║"
echo "╚══════════════════════════════════════╝"
echo ""

COMPOSER_HASH_FILE="storage/framework/cache/.composer-deps.hash"

compute_composer_hash() {
    if [ -f "composer.lock" ]; then
        sha256sum composer.json composer.lock | sha256sum | awk '{print $1}'
    elif [ -f "composer.json" ]; then
        sha256sum composer.json | awk '{print $1}'
    else
        echo ""
    fi
}

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
else
    mkdir -p "$(dirname "$COMPOSER_HASH_FILE")"

    CURRENT_COMPOSER_HASH="$(compute_composer_hash)"
    SAVED_COMPOSER_HASH=""

    if [ -f "$COMPOSER_HASH_FILE" ]; then
        SAVED_COMPOSER_HASH="$(cat "$COMPOSER_HASH_FILE")"
    fi

    if [ ! -f "vendor/autoload.php" ]; then
        echo "▶ Instalando dependencias Composer (vendor ausente)..."
        composer install \
            --no-interaction \
            --prefer-dist \
            --optimize-autoloader \
            --no-progress || exit 1
        echo "$CURRENT_COMPOSER_HASH" > "$COMPOSER_HASH_FILE"
        echo "  ✓ Composer listo"
    elif [ "$CURRENT_COMPOSER_HASH" != "$SAVED_COMPOSER_HASH" ]; then
        echo "▶ Detectado cambio en composer.json/composer.lock. Sincronizando dependencias..."
        composer install \
            --no-interaction \
            --prefer-dist \
            --optimize-autoloader \
            --no-progress || exit 1
        echo "$CURRENT_COMPOSER_HASH" > "$COMPOSER_HASH_FILE"
        echo "  ✓ Dependencias Composer sincronizadas"
    else
        echo "  ✓ Dependencias Composer sin cambios, omitiendo install"
    fi
fi

# ── 4. Copiar .env si no existe ───────────────────────────
if [ ! -f ".env" ]; then
    echo "▶ Copiando .env.example → .env"
    cp .env.example .env
fi

# ── 5. Generar APP_KEY si está vacía ──────────────────────
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=\"\"" .env; then
    echo "▶ Generando APP_KEY..."
    php artisan key:generate --force || echo "  ⚠ APP_KEY ya existe"
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
    php artisan storage:link 2>&1 || echo "  ⚠ Storage link falló"
fi

# ── 8. Limpiar cache de config (evita valores viejos) ─────
echo "▶ Limpiando cache de configuración..."
php artisan config:clear 2>&1 || echo "  ⚠ Config clear falló (puede ser normal)"
echo "  ✓ Config cache limpia"

# ── 9. Migraciones ────────────────────────────────────────
echo "▶ Ejecutando migraciones..."
php artisan migrate --force 2>&1 || echo "  ⚠ Migraciones fallaron (puede ser normal si ya existen)"
echo "  ✓ Migraciones completadas"

# ── 10. Cache según entorno ───────────────────────────────
if [ "$APP_ENV" = "production" ]; then
    echo "▶ Cacheando configuración (producción)..."
    php artisan config:cache 2>&1 || echo "  ⚠ Config cache falló"
    php artisan route:cache 2>&1 || echo "  ⚠ Route cache falló"
    php artisan view:cache 2>&1 || echo "  ⚠ View cache falló"
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
