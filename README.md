# Search Inside a Book — Notas Técnicas

## Stack

- **Livewire + TALL**: componentes reactivos sin SPA; Tailwind vía CDN.
- **BookSearchService**: lógica aislada para facilitar pruebas y mantenimiento.

## Instalación

**Con Sail:**
```bash
cp .env.example .env
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
```

**Sin Docker:**
```bash
composer install && cp .env.example .env
php artisan key:generate && php artisan serve
```

Accede a `http://localhost:8000`.

## Simplificaciones

- Búsqueda con `stripos` sobre un JSON, sin full-text.
- Páginas como PNG, no visor de PDF.
- Tailwind por CDN, sin build.
- Métricas en Cache, sin persistencia.

## Arquitectura

**Backend:**
- `BookSearchService` → búsqueda, snippets, cache.
- `BookController` → sirve imágenes.
- Rutas: `/` (búsqueda), `/page/{page}` (vista), `/page/{page}/image` (PNG).

**Frontend:**
- `BookSearch.php` (Livewire) → estado + interacción.
- `book-search.blade.php` → UI con debounce (`wire:model.debounce.300ms`).
- `page.blade.php` → muestra la página.

## Puntos fuertes

- **Separación clara**: dominio (Service) / estado (Livewire) / UI (Blade).
- **Legible**: métodos pequeños y orientados a intención.
- **Performance suficiente**: cache del índice, lectura única, límite de resultados.
- **Snippet práctico**: contexto + escape HTML + highlight, sin dependencias.
- **Seguridad integrada**: `e()` por defecto y `{!! !!}` solo donde corresponde.
