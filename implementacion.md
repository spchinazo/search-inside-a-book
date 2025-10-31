## Cómo ejecutar y probar localmente (fuera de Docker)

Por defecto, el archivo `.env` está configurado para el entorno Docker/Sail, usando `DB_HOST=pgsql`.
Para ejecutar y probar localmente (usando `php artisan serve`), defina la variable de entorno `DB_HOST` como `127.0.0.1` antes de iniciar el servidor:

- En PowerShell/Windows:
  ```powershell
  $env:DB_HOST="127.0.0.1"
  php artisan serve
  ```
- En Linux/macOS:
  ```bash
  export DB_HOST=127.0.0.1
  php artisan serve
  ```

Así, la aplicación podrá conectarse al PostgreSQL local normalmente.

## Evidencia de pruebas

Se realizaron pruebas del endpoint de búsqueda utilizando Postman, confirmando que la API responde correctamente con los resultados esperados:

![Prueba del endpoint de búsqueda](docs/postman_test.png)

# Implementación técnica

A continuación se documentan los pasos y decisiones tomadas durante la implementación de la funcionalidad de búsqueda en el proyecto "search-inside-a-book".


## Avances realizados (28/10/2025)

- Se creó el controlador `SearchController` con el método `search`, encargado de leer el archivo JSON del libro, filtrar las páginas por el término buscado y devolver los resultados en formato JSON.
- Se añadió la ruta de API `GET /api/search` en `routes/api.php`, apuntando al método `search` del controlador.
- Se implementó la lógica de búsqueda, extrayendo fragmentos de contexto relevantes y asegurando la codificación UTF-8 en las respuestas.
- Se realizaron pruebas exhaustivas del endpoint `/api/search?query=JavaScript`, resolviendo problemas de codificación y garantizando que la API responde correctamente con resultados esperados.
- Se documentó el proceso de limpieza y validación del archivo JSON para evitar errores de UTF-8.


## Implementación de la API de Búsqueda y Página Completa

### Pasos realizados

- Se corrigió un problema de autoload en Laravel: el archivo `SearchController.php` no tenía la etiqueta de apertura `<?php`, lo que impedía el reconocimiento de la clase por Composer. Tras añadir la etiqueta, el endpoint `/api/page/{numero}` funcionó normalmente.
- Se probó el endpoint `/api/page/2` vía Postman, retornando correctamente el contenido de la página.
- Se probó el endpoint `/api/search?query=palabra`, retornando resultados (o vacío, según el término).

### Próximos pasos

- [ ] Mejorar la documentación de los endpoints y ejemplos de uso.
- [ ] Añadir pruebas automatizadas para los endpoints.
- [ ] (Opcional) Implementar paginación o filtros avanzados en la búsqueda.

### Observaciones

- Importante: siempre garantizar que todos los archivos PHP tengan la etiqueta de apertura `<?php` para evitar problemas de autoload en Laravel.
- El JSON de datos debe estar limpio y codificado en UTF-8.

## 1. Lectura de Requisitos (README.md)
- **Objetivo:** Implementar una búsqueda dentro de un libro, mostrando fragmentos y la información sobre dónde se encontró la coincidencia.
- El usuario puede visualizar la página completa al seleccionar un resultado.
- El ejercicio permite enfoque en backend, frontend, mobile o enfoque combinado.
- **Documentación:** Decisiones, trade-offs, limitaciones y plan de evolución deben ser registrados.
- **Entrega:** Vía Merge Request, funcionando localmente, con instrucciones claras de ejecución y pruebas.

- **Stack:** Laravel 12, PHP 8.3+, Docker, Sail, PostgreSQL, Vite.
- **Pasos principales:**
  1. Clonar el fork del repositorio.
  2. Copiar `.env.example` a `.env`.
  3. Ejecutar `composer install`.
  4. Levantar el entorno con `./vendor/bin/sail up -d`.
  5. Generar la clave de la aplicación.
  6. Instalar dependencias JS con `./vendor/bin/sail yarn install`.
  7. Ejecutar `./vendor/bin/sail yarn dev` para desarrollo.
  8. Ejecutar migraciones si es necesario.
  9. Crear el symlink de storage si se usan archivos.
  10. Acceder a la aplicación en http://localhost:8888.

## Etapa: Implementación de la visualización de página completa

- Se implementó el endpoint `/api/page/{numero}` en el backend Laravel, permitiendo al usuario visualizar el contenido completo de una página del libro.
- Se corrigió un problema de autoload del controlador (ausencia de la etiqueta `<?php` al inicio del archivo).
- Probado con éxito vía Postman y curl, retornando correctamente el contenido de la página solicitada.
- Documentado el flujo de búsqueda y visualización de página:
  1. El usuario realiza búsqueda por término usando `/api/search?query=...`.

## Etapa de paginación y visualización de página completa

- Se implementó el endpoint `/api/page/{numero}` en el backend (Laravel) para permitir la visualización del contenido completo de una página del libro.
- El controlador `SearchController` ahora incluye el método `pagina($numero)`, que busca la página solicitada en el archivo JSON y retorna su contenido en formato JSON.

---


![Ejemplo de uso de la API de búsqueda y visualización de página](docs/ejemplo_api_busqueda_pagina.png)

## Pruebas automatizadas de la API

- Se crearon pruebas automatizadas en `tests/Feature/SearchTest.php` para validar los endpoints de búsqueda y visualización de página.
- Las pruebas cubren:
```bash
vendor\bin\phpunit --filter=SearchTest
- Isso garante que a API responde corretamente aos casos esperados e aos erros.

---

## Decisiones técnicas, trade-offs y limitaciones

## Visualización web integrada (Blade)

Se implementó una interfaz web sencilla utilizando Blade (Laravel) para buscar y visualizar resultados de la API:

- La página principal muestra un formulario de búsqueda y lista de resultados paginados, con links para ver la página completa.
- El controlador `SearchWebController` consume la API internamente y renderiza los resultados en la view `search.blade.php`.
- Al clicar en "Ver página completa", se accede a la view `page.blade.php` con el texto completo de la página seleccionada.
1. Acceda a `http://localhost:8888/` (ou la porta configurada) en el navegador.
2. Realice una búsqueda por cualquier término.
3. Navegue por los resultados y acceda a la página completa desde los links.
**Ventajas:**
- Facilita pruebas manuales y presentación visual del proyecto.
- Valoriza la entrega para la evaluación en Publicala.

- Implementar paginação real e filtros avançados na busca.
- Adicionar autenticação e autorização para proteger os endpoints.
- Criar uma interface frontend (web ou mobile) para facilitar a experiência do usuário.
- Adicionar logs e métricas de uso para monitoramento e auditoria.


### 1. Endpoint de búsqueda paginada

curl -G --data-urlencode "query=JavaScript" --data-urlencode "page=1" --data-urlencode "per_page=3" http://localhost:8888/api/search
```
**Respuesta:**
    { "pagina": 3, "contexto": "The third edition of Eloquent JavaScript was made possible by 325 fina" },
    { "pagina": 4, "contexto": " . . . . . . . . .  4 What is JavaScript? . . . . . . . . . . . . . . " }
  ],
  "total": 187,
  "pagina_atual": 1,
  "por_pagina": 3
}
```

### 2. Endpoint de página específica

**Solicitud:**
```
curl http://localhost:8888/api/page/2
```
**Respuesta:**
```
{
  "id": 1,
  "page": 2,
  "text_content": "EloquentJavaScript 3rdedition Marijn Haverbeke",
  ...
}
```

Ambos endpoints respondieron correctamente, comprobando el funcionamiento de la API integrada con la base de datos Docker.

### Captura de pantalla de las pruebas de los endpoints

![Pruebas de los endpoints de búsqueda y página](docs/teste_api_search.png)

La imagen anterior muestra la terminal ejecutando los comandos curl para los endpoints `/api/search` y `/api/page/2`, comprobando el funcionamiento correcto de la API.

---

## Pruebas automatizadas de la API y frontend (Docker)

Se implementaron pruebas Feature para la API y la interfaz Blade, ejecutadas dentro del contenedor Docker.
Todas las pruebas pasaron correctamente, validando la robustez de la solución.


## Pruebas viusal de frontend React

- Evidencia visual del frontend React funcionando:
![Ejemplo de uso de Frontend de búsqueda y visualización de página](docs/evidencia_frontend_react.png)

