# Infra setup (PHP 8.3, Sail runtime)

Esta carpeta contiene solo infraestructura; el código está en `../backend`.

## Variables de entorno
Crea/edita `infrastructure/.env` (junto al docker-compose) con puertos y credenciales, por ejemplo:
```
APP_PORT=8888
VITE_PORT=5173
FORWARD_DB_PORT=5432
DB_DATABASE=publicala_db
DB_USERNAME=publicala_user
DB_PASSWORD=publicala_password
WWWUSER=1000
```
Para Laravel, copia `backend/.env.example` a `backend/.env` y usa los mismos `DB_*`.

## Primeros pasos (host puede seguir en PHP 7.3)
Desde la raíz del repo:
```
cd infrastructure
# 1) Instalar deps PHP dentro del contenedor (genera vendor/ en backend)
docker compose run --rm laravel.test composer install
# 2) Levantar servicios
docker compose up -d
# 2.1) (opcional) Instalar bash en la imagen y usarlo en sesiones interactivas
docker compose exec laravel.test apt-get update
docker compose exec laravel.test apt-get install -y bash
# entrar con bash en vez de sh
docker compose exec -it laravel.test bash
# 3) Claves de la app
docker compose exec laravel.test php artisan key:generate
# 4) Dependencias frontend
docker compose exec laravel.test yarn install --frozen-lockfile
# 5a) Servir assets en dev (HMR)
docker compose exec laravel.test yarn dev --host --port ${VITE_PORT:-5173}
# 5b) O solo construir para generar manifest
docker compose exec laravel.test yarn build
```

## Notas
- El compose monta `../backend` en `/var/www/html` y construye la imagen local `sail-8.3/local` desde `backend/vendor/laravel/sail/runtimes/8.3` con `pull_policy: never` (no requiere pulls externos).
- Si cambias puertos, ajusta `infrastructure/.env` y reinicia (`docker compose down && docker compose up -d`).
- Migrations/tests: `docker compose exec laravel.test php artisan migrate`, `docker compose exec laravel.test php artisan test`.
