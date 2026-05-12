# ============================================================
#  WAYNA — Makefile
#  Uso: make <comando>
#  Ejemplo: make up | make shell | make migrate
# ============================================================

.PHONY: up down restart shell logs build migrate fresh \
        artisan composer require tinker queue-work test \
        redis-cli npm cache-clear ps

# ── Levantar todo ─────────────────────────────────────────
up:
	docker compose up -d --build
	@echo ""
	@echo "  ✓ WAYNA corriendo en http://localhost:9090"
	@echo "  ✓ pgAdmin en        http://localhost:5051"
	@echo ""

# ── Levantar sin rebuild ──────────────────────────────────
start:
	docker compose up -d

# ── Bajar todo ────────────────────────────────────────────
down:
	docker compose down

# ── Reiniciar solo el app ────────────────────────────────
restart:
	docker compose restart wayna_app

# ── Rebuild solo PHP (cuando cambias el Dockerfile) ──────
rebuild:
	docker compose up -d --build wayna_app

# ── Entrar al bash del contenedor Laravel ────────────────
shell:
	docker compose exec wayna_app bash

# ── Ver logs en tiempo real ───────────────────────────────
logs:
	docker compose logs -f wayna_app

# ── Logs de todos los servicios ──────────────────────────
logs-all:
	docker compose logs -f

# ── Estado de contenedores ────────────────────────────────
ps:
	docker compose ps

# ── Artisan: make artisan cmd="migrate:status" ───────────
artisan:
	docker compose exec wayna_app php artisan $(cmd)

# ── Composer: make composer cmd="require laravel/sanctum" ─
composer:
	docker compose exec wayna_app composer $(cmd)

# ── Instalar paquete: make require pkg=laravel/sanctum ───
require:
	docker compose exec wayna_app composer require $(pkg)

# ── Migraciones ───────────────────────────────────────────
migrate:
	docker compose exec wayna_app php artisan migrate

# ── Migraciones + seeders (borra todo) ───────────────────
fresh:
	docker compose exec wayna_app php artisan migrate:fresh --seed

# ── Tinker ───────────────────────────────────────────────
tinker:
	docker compose exec wayna_app php artisan tinker

# ── Worker de colas Redis ────────────────────────────────
queue-work:
	docker compose exec wayna_app php artisan queue:work redis --tries=3

# ── Limpiar cache ────────────────────────────────────────
cache-clear:
	docker compose exec wayna_app php artisan cache:clear
	docker compose exec wayna_app php artisan config:clear
	docker compose exec wayna_app php artisan route:clear
	docker compose exec wayna_app php artisan view:clear

# ── Redis CLI ─────────────────────────────────────────────
redis-cli:
	docker compose exec wayna_redis redis-cli -a redis_secret

# ── Tests ─────────────────────────────────────────────────
test:
	docker compose exec wayna_app php artisan test

# ── NPM desde fuera del contenedor ───────────────────────
npm:
	docker compose exec wayna_node npm $(cmd)
