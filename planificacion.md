Este archivo contiene la planificación inicial del proyecto "search-inside-a-book".
Actualización: revisión inicial para validar el flujo de trabajo del fork y confirmar que los cambios se reflejan correctamente en el repositorio.
# Planificación del ejercicio

## 1. Lectura de requisitos

## 2. Configuración del entorno

## 3. Análisis de los datos
  </div>

  {
    "page": <número de página>,
    "text_content": "<texto completo de la página>"
  - `GET /api/search`: Permite buscar un término en el contenido de todas las páginas, devolviendo resultados paginados y fragmentos de contexto donde aparece la coincidencia.
  - `GET /api/page/{numero}`: Permite obtener el contenido completo de una página específica por su número.

## 6. Visualización de resultados
  - Demuestra integración frontend-backend.
  - Facilita pruebas manuales y presentación visual del proyecto.
  - Valoriza la entrega para la evaluación en Publicala.

![Evidencia de la interfaz Blade funcionando](docs/evidencia_blade_frontend.png)

## 7. Pruebas
  - Búsqueda por término existente y inexistente (API y web)
  - Visualización de página existente e inexistente (API y web)
  - Renderización del formulario y mensajes de la interfaz


Evidencia de pruebas API:
![Evidencia de pruebas API](docs/evidencia_test_api.png)

Evidencia de pruebas frontend Blade:
![Evidencia de pruebas frontend](docs/evidencia_test_frontend.png)

Evidencia de prueba manual con Postman:
![Evidencia de prueba Postman](docs/postman_test.png)

## 8. Ajustes finales y documentación



**Evidencias de pruebas unitarias (TDD):**

<div style="display: flex; gap: 24px; align-items: flex-start;">
  <div>
    <strong>1. Instanciación y atributos del modelo Page:</strong><br>
    <img src="docs/evidencia_test_tdd.png" alt="Evidencia de prueba unitaria" width="1100" />
  </div>
  <div>
    <strong>2. Extracción de snippet/contexto con destaque:</strong><br>
    <img src="docs/evidencia_test_tdd2.png" alt="Evidencia de prueba unitaria 2" width="1100" />
  </div>
</div>



---

## 9. Integración frontend moderno y troubleshooting avanzado

- Se implementó un frontend moderno en React (Vite) con proxy Node.js para servir el build y redirigir `/api` al backend Laravel.
- Se documentaron y resolvieron problemas de proxy, orden de middlewares, diferencias entre ambientes de desarrollo y producción, y cache de PWA.
- Se añadieron logs de depuración en el proxy para facilitar troubleshooting.
- Se corrigió el manifest del PWA añadiendo screenshots para dispositivos móviles.
- Todo el flujo de integración, pruebas y solución de errores está documentado en `implementacion.md` y en este archivo.

---
- **Documentación técnica:** El archivo `implementacion.md` detalla decisiones técnicas, trade-offs, ejemplos de uso de la API, capturas de pruebas y propuesta de evolución futura.

- **Planificación rastreable:** El archivo `planificacion.md` documenta todas las etapas, decisiones y entregables, facilitando la evaluación del flujo de trabajo.


