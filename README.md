# WAYNA — Sistema Web Integral de Ventas, Reservas y Donaciones Gamificadas

> Sistema web para la gestión comercial y administrativa de emprendedores artesanales afiliados a WAYNA en la ciudad de La Paz, Bolivia.

---

## Información del Equipo

| Campo | Detalle |
|---|---|
| **FE+1** | Rubro 2 caso WAYNA |
| **Rubro asignado** | Sistemas de Gestión Comercial para Emprendedores Artesanales |
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

### Lenguajes de Programación
- PHP
- HTML5
- CSS3
- SQL

### Frameworks y Librerías
- **Laravel** — Framework backend principal (patrón MVC)
- **Livewire** — Componentes reactivos frontend integrados con Laravel

### Herramientas de Desarrollo
- **Docker** — Contenedorización del entorno completo (contenedores separados por servicio)
- **Nginx** — Servidor web y proxy inverso dentro del contenedor Docker
- **pgAdmin** — Administración gráfica de la base de datos
- **Jira** — Gestión del backlog, sprints y seguimiento de tareas
- **Git** — Control de versiones

---

##  Base de Datos

| Campo | Detalle |
|---|---|
| **Motor** | PostgreSQL |
| **Administración** | pgAdmin (incluido en el entorno Docker) |

---

##  Arquitectura

### Tipo de Sistema
Plataforma **web responsive** accesible desde computadoras, tablets y dispositivos móviles mediante navegadores modernos.

### Arquitectura Seleccionada
**MVC (Modelo - Vista - Controlador)** implementado con Laravel, con los siguientes servicios:

```
┌─────────────────────────────────────────┐
│              DOCKER                      │
│  ┌──────────┐  ┌──────────┐  ┌───────┐ │
│  │  Nginx   │  │  Laravel │  │  DB   │ │
│  │ (proxy)  │→ │  (MVC)   │→ │  PG   │ │
│  └──────────┘  └──────────┘  └───────┘ │
│                               ┌───────┐ │
│                               │pgAdmin│ │
│                               └───────┘ │
└─────────────────────────────────────────┘
```

---

##  Estructura de Ramas (Git Flow)

```
main                  ← Producción. Solo recibe merges estables desde develop.
│
└── develop           ← Integración. Todo el trabajo se consolida aquí.
    │
    ├── feature/YovaniAndia
    ├── feature/DiegoCondori
    ├── feature/AlexRamires
    └── feature/RafaelOsinaga
```

**Reglas:**
- Nunca hacer commits directos a `main`
- Cada funcionalidad se desarrolla en su `feature/nombre`
- Los Pull Requests se hacen desde `feature/*` hacia `develop`
- Solo cuando `develop` es estable se hace merge a `main`

---

##  Módulos del Sistema

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

##  Metodología

**Scrum** con sprints de 1 semana, gestionado en **Jira**.

| Sprint | Contenido |
|---|---|
| Sprint 1 | Sesión/Auth, Catálogo, QR & Pagos, Reservas |
| Sprint 2 | Donaciones, Administración, Pantallas POS, Búsqueda |

---

##  Instalación y Configuración Local

```bash
# 1. Clonar el repositorio
git clone https://github.com/yovasx/WAYNA_SISTEMA.git
cd wayna

# 2. Copiar variables de entorno
cp .env.example .env

# 3. Levantar contenedores Docker
docker-compose up -d

# 4. Instalar dependencias
docker exec -it wayna_app composer install
docker exec -it wayna_app php artisan key:generate

# 5. Ejecutar migraciones
docker exec -it wayna_app php artisan migrate --seed
```

> Requisitos: Docker Desktop instalado y corriendo.
