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

## Próximos pasos

- Mantener la documentación actualizada ante cualquier ajuste o mejora.
- Considerar agregar validaciones adicionales y manejo de errores más detallado si es necesario.
