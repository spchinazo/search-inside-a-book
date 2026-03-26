## Arquitectura y motivación
- Dividimos el repo en dos carpetas: [backend/](backend) (código Laravel) e [infrastructure/](infrastructure) (solo Docker Compose). La separación se hizo porque en la máquina host no era posible instalar PHP 8.3; ahora todo el runtime vive en contenedores.
- El compose usa la imagen Sail 8.3 y monta [backend/](backend) dentro del contenedor, de modo que puedes trabajar aunque tu host tenga PHP 7.x.
- Ver detalles de puertos/variables en [infrastructure/README.md](infrastructure/README.md).

## Qué se construyó
- Búsqueda dentro del libro (datos en [backend/storage/exercise-files/](backend/storage/exercise-files)), con snippet, número de página y navegación hacia la página completa.
- Vista de resultados con toggles de cuadrícula/lista, paginación, acciones hover (favoritos y marcar como visto) y búsqueda por selección de texto.
- Vista de página con el mismo set de interacciones, favoritos locales (localStorage), botón para abrir/buscar en otra pestaña y selector compacto para saltar de página.
- Persistencia de favoritos y páginas vistas en localStorage para no requerir base de datos.

## Paquetes y stack
- Laravel 12 sobre PHP 8.3 (dentro del contenedor Sail).
- PostgreSQL 15 en Docker (si se habilita DB real; la solución actual usa archivos JSON).
- Vite + Yarn para assets; Bootstrap (via Laravel UI) para estilos base.
- Node 20.x en la imagen Sail; no necesitas Node/PHP en el host.

## Puesta en marcha (vía Docker, recomendado)
> Resumen; el detalle y variables están en [infrastructure/README.md](infrastructure/README.md).

1) Copia variables: `cp infrastructure/.env.example infrastructure/.env` (ajusta puertos si chocas con otros servicios).  
2) Instala dependencias PHP dentro del contenedor (generará `backend/vendor`):
```
cd infrastructure
docker compose run --rm laravel.test composer install
```
3) Sube servicios: `docker compose up -d`
4) Genera clave: `docker compose exec laravel.test php artisan key:generate`
5) Dependencias frontend: `docker compose exec laravel.test yarn install --frozen-lockfile`
6) Servir assets en dev: `docker compose exec laravel.test yarn dev --host --port ${VITE_PORT:-5173}`  
    o construir: `docker compose exec laravel.test yarn build`
7) Acceso: http://localhost:${APP_PORT:-8888}

## Uso rápido (UX)
- Página principal: busca un término (>=2 caracteres), alterna vista cuadrícula/lista y usa los botones hover para marcar favoritos o vistos. La selección de texto dispara un botón flotante para buscar esa frase.
- Página individual: muestra el contenido completo, panel de favoritos arriba, selector de páginas compactas, toggles de vista y paginación de resultados del término. Favoritos/visitados se guardan en localStorage, no requiere DB.
- Navegación limpia: cuando ya estás en una página favorita, su chip y el botón de favorito quedan deshabilitados para evitar clics redundantes.

## Decisiones y dependencias
- Runtime aislado en contenedores por incompatibilidad de PHP 8.3 en el host (motivó separar `backend/` e `infrastructure/`).
- Persistencia simple en archivos JSON de [backend/storage/exercise-files/](backend/storage/exercise-files); la base de datos es opcional.
- Paquetes clave: Laravel 12, Sail (PHP 8.3), PostgreSQL 15 (opcional), Vite + Yarn, Bootstrap via Laravel UI.

## Puesta en marcha (host con PHP 8.3)
Si ya tienes PHP 8.3+ y Composer local, puedes usar Sail directamente desde [backend/](backend):
```
cd backend
cp .env.example .env
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail yarn install
./vendor/bin/sail yarn dev   # o yarn build
```
El puerto por defecto es 8888; ajusta `APP_PORT` en `.env` si necesitas otro valor.

## Notas de revisión
- Los datos del libro viven en [backend/storage/exercise-files/Eloquent_JavaScript.json](backend/storage/exercise-files/Eloquent_JavaScript.json) y [backend/storage/exercise-files/Eloquent_JavaScript_pages/](backend/storage/exercise-files/Eloquent_JavaScript_pages).
- No se requiere base de datos para las features actuales; si la activas, usa las variables `DB_*` compartidas entre [backend/.env](backend/.env) e [infrastructure/.env](infrastructure/.env).
- Tests: `docker compose exec laravel.test php artisan test` o, en host, `./vendor/bin/sail artisan test`.

## AI Usage & Accountability
AI se usó para idear y acelerar, pero todo el código fue revisado manualmente.