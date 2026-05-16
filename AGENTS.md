# AGENTS.md

## Estructura
- La aplicacion real vive en `src/`. La raiz contiene Docker, `Makefile` y documentacion.
- No asumas que `composer` o `npm` corren desde la raiz del repo; los manifiestos estan en `src/`.

## Flujo Preferido
- Trabaja desde la raiz usando Docker o `make`, no con instalaciones locales asumidas.
- Comandos base:
  - `make up`, `make start`, `make down`
  - `make shell`
  - `make artisan cmd="migrate"`
  - `make test`
  - `make npm cmd="run build"`
- Equivalentes directos:
  - `docker compose exec wayna_app php artisan ...`
  - `docker compose exec wayna_app composer ...`
  - `docker compose exec wayna_node npm ...`
- No levantes otro servidor Vite por defecto: `wayna_node` ya corre `npm install && npm run dev`.

## Entorno
- Hay dos capas de entorno:
  - `.env` en la raiz para `docker-compose.yml`
  - `src/.env` para Laravel
- Para ejecucion local con Docker, la fuente real de DB/Redis/URL es `docker-compose.yml` mas la `.env` de la raiz.
- `src/.env.example` sigue bastante cercano al default de Laravel; no lo tomes como descripcion fiel del entorno Docker.

## Runtime Docker
- `docker/php/entrypoint.sh` espera Postgres y Redis, instala dependencias Composer cuando cambian `composer.json` o `composer.lock`, copia `src/.env.example` si falta `.env`, genera `APP_KEY`, crea `storage:link`, limpia config y corre migraciones.
- Si cambias `docker/php/Dockerfile` o la imagen PHP, reconstruye `wayna_app`.

## Arquitectura
- Entrypoints web: `src/routes/web.php`.
- Entrypoints API: `src/routes/api.php`.
- La home usa `App\Http\Controllers\HomeController`.
- Los paneles admin y emprendedor estan montados como rutas Livewire por clase en `App\Livewire\Admin\...` y `App\Livewire\Emprendedor\...`.
- El frontend es Blade + Livewire/Volt + Tailwind con JS puntual en `src/resources/js/app.js`; no asumas React, Vue o Inertia.
- Varias rutas publicas siguen como placeholders en `src/routes/web.php` (`catalogo`, `emprendedores`, `donar`, `carrito`, `notificaciones`, `panel/usuario`).

## Auth y Roles
- `App\Models\User` usa la tabla `usuarios`, no `users`.
- La autenticacion usa `password_hash`; el modelo expone el atributo `password` y `getAuthPassword()`.
- El middleware `rol` se registra en `src/bootstrap/app.php` y resuelve a `App\Http\Middleware\VerificarRol`.
- Los checks de rol pasan por `User::tieneRol(...)`, que mapea nombres como `admin`, `usuario` y `emprendedor` a roles en mayusculas guardados en BD.

## Tests y Verificacion
- Backend rapido: `docker compose exec wayna_app php artisan test`
- Test puntual: `docker compose exec wayna_app php artisan test --filter ProfileTest`
- `src/phpunit.xml` usa SQLite en memoria, cache array, session array y cola sync; los tests no dependen de Postgres ni Redis.
- Verificacion frontend: `docker compose exec wayna_node npm run build`
- Hay `laravel/pint` en `src/composer.json`, pero no hay config de ESLint, Prettier, TypeScript ni workflows CI en el repo.

## Seeds y Datos Demo
- `src/database/seeders/DatabaseSeeder.php` siempre llama a `WaynaRealisticSeeder`.
- `php artisan migrate:fresh --seed` carga datos demo realistas, no una base vacia.
- Credenciales verificadas del seeder:
  - `admin@admin.gmail.com` / `admin123`
  - `emprendedor@wayna.bo` / `admin123`
  - `usuario@wayna.bo` / `admin123`

## Busqueda y Edicion
- No edites artefactos generados:
  - `src/storage/framework/views`
  - `src/public/build`
  - `src/public/hot`
  - `src/bootstrap/cache`
  - `src/vendor`
  - `src/node_modules`
- Si `grep` devuelve vistas compiladas en `src/storage/framework/views`, edita la fuente real en `src/resources/views`.

## Prioridad de Fuentes
- Confia primero en `docker-compose.yml`, `Makefile`, `src/composer.json`, `src/phpunit.xml`, `src/routes/*.php` y `docker/php/entrypoint.sh`.
- El `README.md` es util para contexto, pero contiene indicaciones de frontend/HMR que no coinciden del todo con `src/vite.config.js`.
