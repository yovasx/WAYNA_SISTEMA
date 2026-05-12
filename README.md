# WAYNA — Sistema Web de Ventas, Reservas y Donaciones

Sistema web integral para emprendedores artesanales afiliados a WAYNA en La Paz, Bolivia.

---

## Stack tecnológico

| Servicio    | Imagen                | Puerto local | Puerto interno |
|-------------|----------------------|-------------|----------------|
| Laravel App | php:8.3-fpm          | —           | 9000 (FPM)     |
| Nginx       | nginx:1.27-alpine    | **9090**    | 80             |
| PostgreSQL  | postgres:17.2        | **5433**    | 5432           |
| pgAdmin 4   | dpage/pgadmin4:8.14  | **5051**    | 80             |
| Redis       | redis:7.4-alpine     | **6380**    | 6379           |
| Node / Vite | node:20-alpine       | **5173**    | 5173           |

> Los puertos locales evitan conflictos con servicios que ya tengas corriendo.

---

## Requisitos previos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/Mac) o Docker Engine + Compose (Linux)
- Git
- **No necesitas** PHP, Node, Composer ni PostgreSQL instalados localmente

---

## Levantar el proyecto (primera vez)

```bash
# 1. Clonar el repositorio
git clone https://github.com/yovasx/WAYNA_SISTEMA.git
cd WAYNA_SISTEMA

# 2. Copiar variables de entorno
cp .env.example .env

# 3. Levantar todo (build + migraciones automáticas)
docker compose up -d --build
```

Eso es todo. El entrypoint se encarga de:
- Instalar dependencias de Composer
- Generar APP_KEY
- Ejecutar migraciones
- Ajustar permisos de storage

Espera ~60 segundos la primera vez (descarga de imágenes).

---

## Accesos

| Servicio   | URL                        | Credenciales                    |
|-----------|---------------------------|---------------------------------|
| WAYNA App | http://localhost:9090      | —                               |
| pgAdmin   | http://localhost:5051      | admin@wayna.local / admin123    |
| Redis     | localhost:6380             | password: redis_secret          |
| PostgreSQL| localhost:5433             | wayna_user / wayna_secret       |

### Conectar pgAdmin a la base de datos
En pgAdmin agrega un servidor con:
- **Host:** `wayna_postgres` (nombre del contenedor)
- **Port:** `5432`
- **Database:** `wayna`
- **Username:** `wayna_user`
- **Password:** `wayna_secret`

---

## Comandos del equipo (Make)

```bash
make up           # Levantar todo con rebuild
make start        # Levantar sin rebuild (más rápido)
make down         # Bajar todos los contenedores
make shell        # Entrar al bash de Laravel
make logs         # Ver logs de PHP en tiempo real
make logs-all     # Ver logs de todos los servicios
make ps           # Estado de los contenedores

make migrate      # Ejecutar migraciones
make fresh        # Borrar todo y re-migrar con seeders
make tinker       # Laravel Tinker

make cache-clear  # Limpiar toda la caché
make queue-work   # Iniciar worker de colas Redis
make redis-cli    # CLI de Redis

# Instalar un paquete de Composer
make require pkg=laravel/sanctum

# Cualquier comando artisan
make artisan cmd="make:model Producto -mcr"

# Cualquier comando composer
make composer cmd="dump-autoload"
```

> **Windows sin Make:** usa los comandos directamente con `docker compose exec wayna_app php artisan ...`

---

## Desarrollo del frontend (Livewire + Vite + Tailwind)

El contenedor `wayna_node` corre `npm run dev` automáticamente con HMR activo.  
No necesitas hacer nada extra — los cambios en CSS/JS se reflejan en tiempo real.

Si necesitas instalar un paquete npm:
```bash
make npm cmd="install alpinejs"
```

---

## Solución de problemas frecuentes

### "El contenedor no inicia"
```bash
make logs   # Ver el error exacto
```

### "Error de permisos en storage"
```bash
make shell
chmod -R 775 storage bootstrap/cache
```

### "Vite no conecta / HMR no funciona"
Verifica que `vite.config.js` tenga `host: '0.0.0.0'` y `hmr.host: 'localhost'`.  
Ya está configurado por defecto en este repositorio.

### "Quiero agregar un nuevo paquete Composer"
```bash
make require pkg=nombre/paquete
# No necesitas reiniciar ningún contenedor
```

### "Cambié el Dockerfile o docker-compose.yml"
```bash
make rebuild   # Solo reconstruye el contenedor PHP
# o
make up        # Reconstruye todo
```

---

## Estructura del proyecto

```
WAYNA_SISTEMA/
├── docker/
│   ├── php/
│   │   ├── Dockerfile        ← Imagen PHP con todas las extensiones
│   │   └── entrypoint.sh     ← Automatiza composer, migraciones, etc.
│   └── nginx/
│       └── default.conf      ← Config Nginx con soporte Livewire/Vite
├── src/                      ← Proyecto Laravel completo
│   └── vite.config.js        ← Configurado para Docker HMR
├── docker-compose.yml
├── .env.example
├── Makefile
└── README.md
```

---

## Equipo

Proyecto desarrollado con metodología Scrum para WAYNA — La Paz, Bolivia.
