# Guía de Presentación y Documentación Final

## 1. Objetivo del Ejercicio

Implementar una búsqueda eficiente dentro de un libro digital, permitiendo al usuario buscar términos, visualizar fragmentos de contexto y acceder a la página completa donde se encontró la coincidencia. El proyecto utiliza Laravel 12 (PHP 8.3+), PostgreSQL (Docker/Sail), Vite, Bootstrap y React.

---

## 2. Estructura de Carpetas y Archivos Creados

- `app/Http/Controllers/SearchController.php`  
  Controlador principal de la API de búsqueda y visualización de páginas.

- `routes/api.php`  
  Rutas de API: `/api/search` y `/api/page/{numero}`.

- `app/Http/Controllers/SearchWebController.php`  
  Controlador para la interfaz web Blade.

- `resources/views/search.blade.php`  
  Página de búsqueda web (Blade).

- `resources/views/page.blade.php`  
  Página de visualización completa (Blade).

- `storage/exercise-files/Eloquent_JavaScript_clean.json`  
  Archivo de datos del libro.

- `apps/web/`  
  Frontend React (Vite), con proxy al backend.

- `docs/evidencia_frontend_react.png`  
  Evidencia visual del frontend React funcionando.

- `implementacion.md`  
  Documentación técnica detallada (en español).

- `planificacion.md`  
  Planificación y organización del proyecto.

---

## 3. Comandos Principales Utilizados


### Entorno y dependencias (Backend Laravel)
```sh
cp .env.example .env
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan storage:link
```

### Frontend React (apps/web)
```sh
cd apps/web
npm install

# Desarrollo (hot reload, porta 5173/5174)
npm run dev
# Acceder a http://localhost:5173/ o http://localhost:5174/

# Producción (build y servidor Node en porta 3000)
npm run build
node server.js
# Acceder a http://localhost:3000/
```

### Desarrollo backend
```sh
./vendor/bin/sail artisan serve    # Servidor Laravel (opcional)
```

### Pruebas
```sh
./vendor/bin/sail artisan test
./vendor/bin/sail artisan test --filter=SearchTest
```

### Base de datos
```sh
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan migrate:fresh
```

---

## 4. Flujo de Implementación

- Creación de los endpoints de búsqueda y visualización de página.
- Implementación de la interfaz web (Blade) y frontend React.
- Configuración del proxy para integración frontend-backend.
- Pruebas automatizadas y manuales (Postman, curl, PHPUnit).
- Documentación detallada de cada etapa, decisiones técnicas y troubleshooting.
- Evidencias visuales incluidas en la documentación.

---

## 5. Evidencias y Resultados

- Capturas y evidencias de pantallas y pruebas en el archivo `implementacion.md`.
- Imagen del frontend React funcionando en la sección principal de evidencias.
- Todos los endpoints e interfaces probados y validados.

---

## 6. Buenas Prácticas y Diferenciales

- Código limpio, modular y documentado.
- Uso de Docker/Sail para garantizar un entorno estandarizado.
- Pruebas automatizadas cubriendo los principales flujos.
- Documentación 100% en español.
- Flujo de versionado Git y Merge Request en GitLab.

---

## 7. Sugerencia de Presentación

1. Explicar el objetivo del ejercicio y el contexto del desafío.
2. Mostrar la estructura del proyecto y destacar los archivos principales.
3. Demostrar el funcionamiento de los endpoints (Postman/curl).
4. Presentar la interfaz web (Blade) y el frontend React.
5. Mostrar las evidencias visuales y las pruebas realizadas.
6. Destacar buenas prácticas, organización y documentación.
7. Comentar sobre el flujo de trabajo con Git y Merge Request.
8. Estar preparado para responder dudas técnicas sobre decisiones, trade-offs y posibles evoluciones.

---

¡Listo para presentar y defender el proyecto ante la empresa!
