# WAYNA — Sistema Web Integral de Ventas, Reservas y Donaciones Gamificadas

> Sistema web para la gestión comercial y administrativa de emprendedores artesanales afiliados a WAYNA en la ciudad de La Paz, Bolivia.

---

## Información del Equipo

| Campo | Detalle |
|---|---|
| **FE+1** | Rubro 2 caso WAYNA |
| **Rubro asignado** | Sistemas de Gestión Comercial para Emprendedores Artesanales |
| **Universidad** | Universidad Privada "Franz Tamayo" — UNIFRANZ |
| **Facultad** | Facultad de Ingeniería |
| **Carrera** | Ingeniería en Sistemas |
| **Año** | 2026 |

---

## Integrantes

| Nombre | Rol en el Proyecto |
|---|---|
| Yovani Paolo Andia Quispe | Developer |
| Diego Condori Plata | Developer |
| Alex Ramires | Developer |
| Rafael Osinaga | Scrum Master / Developer |

> **Product Owner:** Organización WAYNA (cliente)

---

## Stack Tecnológico

| Servicio | Imagen | Puerto local | Puerto interno |
|---|---|---|---|
| Laravel App | php:8.3-fpm | — | 9000 (FPM) |
| Nginx | nginx:1.27-alpine | **9090** | 80 |
| PostgreSQL | postgres:17.2 | **5433** | 5432 |
| pgAdmin 4 | dpage/pgadmin4:8.14 | **5051** | 80 |
| Redis | redis:7.4-alpine | **6380** | 6379 |
| Node / Vite | node:20-alpine | **5173** | 5173 |

> Los puertos locales evitan conflictos con servicios que ya tengas corriendo.

### Lenguajes de Programación
- PHP
- HTML5
- CSS3
- SQL

### Frameworks y Librerías
- **Laravel** — Framework backend principal (patrón MVC)
- **Livewire** — Componentes reactivos frontend integrados con Laravel
- **Tailwind CSS + Vite** — Estilos y bundling con HMR

---

## Base de Datos

| Campo | Detalle |
|---|---|
| **Motor** | PostgreSQL 17.2 |
| **Administración** | pgAdmin 4 (incluido en el entorno Docker) |
| **Características** | Almacenamiento relacional, robusto y escalable con backups automáticos diarios |

---

## Arquitectura

### Tipo de Sistema
Plataforma **web responsive** accesible desde computadoras, tablets y dispositivos móviles mediante navegadores modernos.

### Arquitectura Seleccionada
**MVC (Modelo - Vista - Controlador)** implementado con Laravel, con los siguientes servicios contenerizados:

```
┌──────────────────────────────────────────────────────┐
│                      DOCKER                           │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────┐ │
│  │  Nginx   │  │  Laravel │  │ PostgreSQL│  │Redis │ │
│  │ (proxy)  │→ │  (MVC)   │→ │  + pgAdmin│  │      │ │
│  └──────────┘  └──────────┘  └──────────┘  └──────┘ │
│                               ┌──────────┐           │
│                               │   Node   │           │
│                               │  (Vite)  │           │
│                               └──────────┘           │
└──────────────────────────────────────────────────────┘
```

---

## Módulos del Sistema

| Módulo | Descripción |
|---|---|
| Gestión de Emprendedores | Registro, perfiles, productos y datos comerciales |
| Catálogo Digital | Visualización pública por categorías, precios y disponibilidad |
| Ventas | Registro de pedidos, inventario y comprobantes |
| Reservas | Talleres, experiencias culturales y productos |
| Donaciones Gamificadas | Puntos, insignias, rankings y certificados |
| Pagos QR / NFC | Procesamiento de pagos digitales y microtransacciones |
| Panel Administrativo | Control centralizado para admin y emprendedores |
| Reportes | Exportación dinámica en PDF y Excel |
| Pantallas POS | Menú restaurante, catálogo mercado y modo kiosco |

---

## Metodología

**Scrum** con sprints de 1 semana, gestionado en **Jira**.

| Sprint | Contenido |
|---|---|
| Sprint 1 | Sesión/Auth, Catálogo, QR & Pagos, Reservas |
| Sprint 2 | Donaciones, Administración, Pantallas POS, Búsqueda |

---

## Estructura de Ramas (Git Flow)

```
main                  ← Producción. Solo recibe merges estables desde develop.
│
└── develop           ← Integración. Todo el trabajo se consolida aquí.
    │
    ├── feature/Yovani
    ├── feature/Diego
    ├── feature/Alex
    └── feature/Rafael
```

**Reglas:**
- Nunca hacer commits directos a `main`
- Cada funcionalidad se desarrolla en su `feature/nombre`
- Los Pull Requests se hacen desde `feature/*` hacia `develop`
- Solo cuando `develop` es estable se hace merge a `main`

---

## Requisitos Previos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/Mac) o Docker Engine + Compose (Linux)
- Git
- **No necesitas** PHP, Node, Composer ni PostgreSQL instalados localmente

---

## Instalación y Configuración Local

```bash
# 1. Clonar el repositorio
git clone https://github.com/yovasx/WAYNA_SISTEMA.git
cd WAYNA_SISTEMA

# 2. Copiar variables de entorno
cp .env.example .env

# 3. Levantar todo (build + migraciones automáticas)
docker compose up -d --build
```

El entrypoint se encarga automáticamente de:
- Instalar dependencias de Composer
- Generar APP_KEY
- Ejecutar migraciones
- Ajustar permisos de storage

> Espera ~60 segundos la primera vez (descarga de imágenes).

---

## Accesos

| Servicio | URL | Credenciales |
|---|---|---|
| WAYNA App | http://localhost:9090 | — |
| pgAdmin | http://localhost:5051 | admin@wayna.local / admin123 |
| Redis | localhost:6380 | password: redis_secret |
| PostgreSQL | localhost:5433 | wayna_user / wayna_secret |

### Conectar pgAdmin a la base de datos
En pgAdmin agrega un servidor con:
- **Host:** `wayna_postgres` (nombre del contenedor)
- **Port:** `5432`
- **Database:** `wayna`
- **Username:** `wayna_user`
- **Password:** `wayna_secret`

---

## Comandos Docker

### Gestión de contenedores

```bash
# Levantar todo con rebuild
docker compose up -d --build

# Levantar sin rebuild (más rápido)
docker compose start

# Bajar todos los contenedores
docker compose down

# Ver estado de los contenedores
docker compose ps

# Ver logs de PHP en tiempo real
docker compose logs -f wayna_app

# Ver logs de todos los servicios
docker compose logs -f
```

### Base de datos

```bash
# Ejecutar migraciones
docker compose exec wayna_app php artisan migrate

# Borrar todo y re-migrar con seeders
docker compose exec wayna_app php artisan migrate:fresh --seed
```

### Desarrollo

```bash
# Entrar al bash de Laravel
docker compose exec wayna_app bash

# Laravel Tinker
docker compose exec wayna_app php artisan tinker

# Limpiar toda la caché
docker compose exec wayna_app php artisan optimize:clear

# Iniciar worker de colas Redis
docker compose exec wayna_app php artisan queue:work

# CLI de Redis
docker compose exec wayna_redis redis-cli
```

### Instalar paquetes

```bash
# Instalar un paquete de Composer
docker compose exec wayna_app composer require nombre/paquete

# Cualquier comando artisan
docker compose exec wayna_app php artisan make:model Producto -mcr

# Instalar un paquete npm
docker compose exec wayna_node npm install alpinejs
```

---

## Desarrollo del Frontend (Livewire + Vite + Tailwind)

El contenedor `wayna_node` corre `npm run dev` automáticamente con HMR activo.  
No necesitas hacer nada extra — los cambios en CSS/JS se reflejan en tiempo real.

Verifica que `vite.config.js` tenga `host: '0.0.0.0'` y `hmr.host: 'localhost'`.  
Ya está configurado por defecto en este repositorio.

---

## Solución de Problemas Frecuentes

### "El contenedor no inicia"
```bash
docker compose logs -f wayna_app   # Ver el error exacto
```

### "Error de permisos en storage"
```bash
docker compose exec wayna_app bash
chmod -R 775 storage bootstrap/cache
```
### "Error no cargan diseños de la pagina (se ve html solamente) aplicalo cuando hagas cambios grandes en la views"
```bash
docker compose run --rm wayna_node npm run buil
```
### "Error SQLexception"
```bash
docker compose exec wayna_app php artisan migrate:fresh --seed
docker compose exec wayna_app php artisan db:seed
```
### "error aun no cargan diseños de las vistas"
Elimina el archivo hot de public
### "Vite no conecta / HMR no funciona"
Verifica la configuración de `vite.config.js` (ver sección Frontend).

### "Cambié el Dockerfile o docker-compose.yml"
```bash
# Solo reconstruir el contenedor PHP
docker compose up -d --build wayna_app

# Reconstruir todo
docker compose up -d --build
```

---

## Estructura del Proyecto

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

