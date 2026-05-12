FROM php:8.3-fpm

# ============================================================
#  WAYNA — PHP 8.3 FPM
#  Todo instalado desde el build para que el equipo
#  no tenga que buscar dependencias manualmente.
# ============================================================

# ── 1. Dependencias del sistema ───────────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libgmp-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ── 2. Extensiones PHP ────────────────────────────────────
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        xml \
        intl \
        opcache \
        gmp

# ── 3. Redis (extensión PECL) ─────────────────────────────
RUN pecl install redis \
    && docker-php-ext-enable redis

# ── 4. Composer 2 ─────────────────────────────────────────
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# ── 5. php.ini optimizado para Laravel ───────────────────
RUN { \
    echo "opcache.enable=1"; \
    echo "opcache.revalidate_freq=0"; \
    echo "opcache.validate_timestamps=1"; \
    echo "opcache.max_accelerated_files=10000"; \
    echo "opcache.memory_consumption=192"; \
    echo "opcache.interned_strings_buffer=16"; \
    echo "opcache.fast_shutdown=1"; \
    echo "upload_max_filesize=20M"; \
    echo "post_max_size=20M"; \
    echo "memory_limit=512M"; \
    echo "max_execution_time=120"; \
} > /usr/local/etc/php/conf.d/wayna.ini

# ── 6. Directorio de trabajo ──────────────────────────────
WORKDIR /var/www/html

# ── 7. Entrypoint ─────────────────────────────────────────
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
