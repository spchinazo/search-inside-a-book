# Features - Arquitectura Vertical Slice

## Sobre el Proyecto

Este proyecto es un ejercicio técnico para Publica.la que implementa un **buscador de contenido dentro de libros digitales en formato PDF**. La aplicación permite a los usuarios buscar texto específico dentro de documentos PDF, visualizar los resultados con highlights (resaltados) y navegar entre las coincidencias encontradas.

### Stack Tecnológico

El proyecto utiliza:
- **Backend**: Laravel 12 con PHP 8.3+
- **Frontend**: Vue.js 3 (Composition API) con Vite
- **Estilos**: Tailwind CSS
- **Visualización PDF**: Tato30/vue-pdf (librería de renderizado de PDFs)

### ¿Por qué Vue.js y Tailwind CSS?

Se decidió utilizar Vue.js con Tailwind CSS porque **el equipo ya estaba familiarizado con este stack tecnológico**, lo que permite un desarrollo más rápido y un código más fácil de entender y mantener. La curva de aprendizaje es mínima y la productividad es máxima cuando se trabaja con herramientas conocidas.

### ¿Por qué Tato30/vue-pdf?

Para la visualización de PDFs se decidió utilizar una **librería específica de Vue.js** (tato30/vue-pdf) en lugar de otras alternativas por las siguientes razones:

#### Ventajas de usar una librería de Vue.js

1. **Mejor control sobre el PDF**: Al estar integrada nativamente con Vue.js, se tiene control total sobre el ciclo de vida del componente, la reactividad y la gestión del estado del documento
2. **Mantenimiento más limpio a largo plazo**: El código permanece dentro del ecosistema de Vue.js, facilitando:
   - Actualizaciones del framework sin conflictos
   - Debugging más intuitivo con Vue DevTools
   - Consistencia en el patrón de código
   - Menor curva de aprendizaje para nuevos desarrolladores del equipo

#### Características específicas de tato30/vue-pdf

1. **Documentación clara y completa**: Incluye ejemplos prácticos y API bien documentada
2. **Actualizaciones constantes**: Mantenimiento activo y soporte para Vue 3
3. **Funcionalidad de highlights**: Incluye de forma nativa la capacidad de resaltar texto dentro del PDF, lo cual es esencial para mostrar los resultados de búsqueda
4. **Compatibilidad**: Funciona perfectamente con Vue 3 Composition API y Vite
5. **Rendimiento**: Renderizado eficiente de documentos PDF grandes
6. **Integración reactiva**: Los cambios en las props (página, zoom, highlights) se actualizan automáticamente gracias a la reactividad de Vue

#### Alternativas consideradas

Se evaluaron otras opciones como:
- **PDF.js directo**: Requiere más código boilerplate y manejo manual del DOM
- **Librerías genéricas de JavaScript**: No aprovechan la reactividad de Vue
- **Soluciones de backend**: Generarían mayor carga en el servidor y peor experiencia de usuario

La decisión de usar tato30/vue-pdf garantiza un código más **mantenible, escalable y alineado con el stack tecnológico** del proyecto.

## ¿Qué es la Arquitectura Vertical Slice?

La Arquitectura Vertical Slice organiza el código por **funcionalidades o capacidades de negocio** en lugar de por capas técnicas. Cada "slice" (rebanada) contiene todo el código necesario para implementar una característica específica, incluyendo componentes, composables, utilidades y tipos.

### ¿Por qué se optó por una estructura vertical?

Se eligió esta arquitectura pensando en la **escalabilidad futura del proyecto**. Basándome en el tipo de aplicación y las funcionalidades que probablemente necesitará Publica.la en el futuro, la arquitectura vertical permite:

1. **Crecimiento ágil**: Agregar nuevas funcionalidades sin afectar las existentes
2. **Equipos paralelos**: Múltiples desarrolladores pueden trabajar en diferentes features sin conflictos
3. **Mantenimiento localizado**: Los cambios quedan contenidos en un solo slice
4. **Reutilización clara**: Cada feature expone una API pública definida
5. **Testing independiente**: Se pueden probar features completas de forma aislada

La arquitectura se adaptó lo mejor posible al proyecto Laravel/Vue.js existente, manteniendo la estructura de Laravel en el backend y aplicando Vertical Slice en el frontend.

## Beneficios de esta Arquitectura

- **Cohesión de Features**: Todo el código relacionado vive junto
- **Navegación más fácil**: Encuentras todo sobre una funcionalidad en un solo lugar
- **Mejor encapsulación**: Las features son autocontenidas e independientes
- **Desarrollo paralelo**: Los equipos pueden trabajar en diferentes features sin conflictos
- **Testing más simple**: Prueba features completas de forma aislada
- **Refactoring simplificado**: Los cambios están localizados en un solo slice

## Estructura de Directorios

```
features/
├── document-viewer/           # Feature de visualización de documentos PDF
│   ├── components/
│   │   └── DocumentViewer.vue
│   ├── composables/
│   │   ├── useDocumentInfo.js      # Gestión de metadata del documento
│   │   ├── useDocumentSearch.js    # Funcionalidad de búsqueda
│   │   ├── useDocumentNavigation.js # Navegación entre páginas
│   │   └── useDocumentZoom.js      # Controles de zoom
│   └── index.js                    # Exportaciones públicas (API del feature)
│
├── search/                    # Feature de búsqueda
│   ├── components/
│   │   ├── SearchInput.vue          # Input de búsqueda
│   │   └── SearchPanel.vue          # Panel de resultados
│   ├── composables/
│   │   ├── useSearch.js             # Estado y lógica de búsqueda
│   │   └── useSearchPanel.js        # Control del panel lateral
│   └── index.js                     # Exportaciones públicas
│
├── index.js                   # Punto de exportación central
└── README.md                  # Este archivo
```

## Estructura de cada Feature

Cada feature slice sigue esta estructura:

### 1. **components/**
Contiene componentes Vue específicos de esta feature. Los componentes deben:
- Ser autocontenidos
- Usar composables del mismo feature
- Importar componentes de otros features vía sus archivos index

### 2. **composables/**
Contiene composables de Vue (composition functions) que encapsulan:
- Gestión de estado
- Lógica de negocio
- Efectos secundarios (llamadas API, etc.)
- Lógica reutilizable

**Convención de nombres**: `use[NombreFeature][Capacidad].js`

Ejemplos: `useDocumentZoom.js`, `useDocumentNavigation.js`

### 3. **index.js**
API pública del feature. Exporta:
- Componentes (como exportaciones nombradas)
- Composables (como exportaciones nombradas)
- Solo lo que debe ser usado por otros features

## Ejemplos de Uso

### Importar un Componente

```javascript
// ✅ Bueno: Importar desde el index del feature
import { DocumentViewer } from '@/features';

// ✅ También válido: Importar desde feature específico
import { DocumentViewer } from '@/features/document-viewer';

// ❌ Malo: No importar desde rutas internas
import DocumentViewer from '@/features/document-viewer/components/DocumentViewer.vue';
```

### Importar un Composable

```javascript
// ✅ Bueno: Importar desde el index del feature
import { useDocumentZoom } from '@/features/document-viewer';

// ❌ Malo: No importar desde rutas internas
import { useDocumentZoom } from '@/features/document-viewer/composables/useDocumentZoom';
```

### Usar Múltiples Features

```javascript
import { DocumentViewer, SearchInput } from '@/features';
```

## Mejores Prácticas de Composables

### 1. **Responsabilidad Única**
Cada composable debe manejar un aspecto del feature:

```javascript
// ✅ Bueno: Responsabilidad enfocada
useDocumentZoom.js         // Solo maneja zoom
useDocumentNavigation.js   // Solo maneja navegación

// ❌ Malo: Demasiadas responsabilidades
useDocument.js  // Maneja zoom, navegación, búsqueda, etc.
```

### 2. **Documentación JSDoc Completa**
Todos los composables y métodos deben tener JSDoc:

```javascript
/**
 * @summary Composable para gestionar el zoom del documento
 * @description Maneja el nivel de zoom, texto de zoom y controles de zoom
 * @returns {Object} Estado y métodos de zoom
 */
export const useDocumentZoom = () => {
  // ...
};
```

### 3. **Retornar Objetos Desestructurables**
Retorna objetos con propiedades nombradas para fácil desestructuración:

```javascript
// ✅ Bueno
export const useDocumentZoom = () => {
  return {
    zoomText,
    scale,
    zoomIn,
    zoomOut,
  };
};

// Uso
const { zoomIn, zoomOut } = useDocumentZoom();
```

### 4. **camelCase para Variables**
Todas las variables, funciones y composables usan camelCase:

```javascript
// ✅ Bueno
const zoomText = ref('100%');
const handleZoomInput = () => {};

// ❌ Malo
const zoom_text = ref('100%');
const handle_zoom_input = () => {};
```

## Agregar un Nuevo Feature

1. Crear directorio del feature: `features/[nombre-feature]/`
2. Agregar subdirectorios: `components/`, `composables/`
3. Crear componentes en `components/`
4. Extraer lógica en composables en `composables/`
5. Crear `index.js` para exportar la API pública
6. Agregar exportaciones al `features/index.js` principal

## Reglas de Organización del Código

### Dentro de un Feature

```javascript
// Estructura Composition API
<script setup>
// 1. Imports
import { ref, computed, watch, onMounted } from 'vue';
import { useFeatureComposable } from '../composables/useFeatureComposable';

// 2. Props
const props = defineProps({...});

// 3. Emits
const emit = defineEmits([...]);

// 4. Variables reactivas (incluyendo composables)
const { state, methods } = useFeatureComposable();
const localState = ref(null);

// 5. Propiedades computadas
const computedValue = computed(() => {...});

// 6. Métodos
const handleClick = () => {...};

// 7. Watchers
watch(source, callback);

// 8. Lifecycle Hooks
onMounted(() => {...});

// 9. Expose (si es necesario)
defineExpose({...});
</script>
```

## Dependencias entre Features

- Los features deben estar **débilmente acoplados**
- Los features pueden importar de otros features vía su API pública (index.js)
- Evitar dependencias circulares entre features
- Las utilidades compartidas van en el directorio `/utils`

## Testing

Cada feature puede ser probado independientemente:

```javascript
// Probar un composable
import { useDocumentZoom } from '@/features/document-viewer';

describe('useDocumentZoom', () => {
  it('debería hacer zoom in', () => {
    const { zoomIn, scale } = useDocumentZoom();
    // lógica del test
  });
});
```

## Mejoras Futuras

Esta sección describe las oportunidades de mejora identificadas durante el desarrollo del proyecto. Algunas son correcciones de limitaciones actuales, otras son expansiones de funcionalidad que agregarían valor significativo a la aplicación.

---

### 1. **Responsive Design / Optimización Mobile**

**Contexto**: La aplicación actual está optimizada para escritorio. En dispositivos móviles la experiencia puede mejorarse significativamente.

**Implementación sugerida**:
- **Controles táctiles optimizados**: Botones más grandes y espaciados para navegación con dedos
- **Layout adaptativo**: Panel de búsqueda colapsable en móviles que no compita por espacio con el PDF
- **Gestos nativos**:
  - Pinch-to-zoom para controlar el nivel de zoom
  - Swipe horizontal para cambiar de página
  - Tap doble para centrar y hacer zoom rápido
- **Menú hamburguesa**: Ocultar controles secundarios y mostrarlos bajo demanda
- **Vista vertical optimizada**: Adaptar el renderizado del PDF para scroll vertical continuo en móviles

**Beneficio**: Aumentar el alcance de la aplicación permitiendo uso efectivo en tablets y smartphones.

---

### 2. **Servicio Centralizado de API Frontend**

**Contexto**: Actualmente las llamadas HTTP se realizan directamente con `fetch` o axios de forma distribuida. Esto dificulta el manejo consistente de errores y la implementación de funcionalidades transversales.

**Implementación sugerida**:
```javascript
// services/ApiService.js
class ApiService {
  constructor() {
    this.baseURL = import.meta.env.VITE_API_URL;
    this.defaultHeaders = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
  }

  /**
   * @summary Realiza petición HTTP con manejo centralizado
   * @description Intercepta errores, aplica retry logic y cachea respuestas
   * @param {string} endpoint - Ruta del endpoint
   * @param {Object} options - Opciones de fetch
   * @returns {Promise<Object>} Respuesta parseada
   */
  async request(endpoint, options = {}) {
    // Interceptores, retry, cache, manejo de tokens
  }
}
```

**Características clave**:
- **Interceptores de request/response**: Agregar tokens de autenticación automáticamente
- **Manejo centralizado de errores**: Mostrar notificaciones consistentes al usuario
- **Cache inteligente**: Evitar peticiones duplicadas en corto tiempo
- **Retry automático**: Reintentar peticiones fallidas con backoff exponencial
- **Request cancellation**: Cancelar peticiones obsoletas (por ejemplo, al cambiar de búsqueda rápidamente)
- **Loading states**: Gestionar estados de carga de forma centralizada

**Beneficio**: Código más limpio, mantenible y comportamiento consistente en toda la aplicación.

---

### 3. **Optimización de Carga de Archivos (Backend + CDN)**

**Contexto**: Los PDFs se sirven actualmente desde el servidor Laravel. Para archivos grandes o muchos usuarios concurrentes, esto puede crear cuellos de botella.

**Implementación sugerida**:

**Backend (Laravel)**:
- Migrar almacenamiento de PDFs a **AWS S3** o **Cloudflare R2**
- Implementar **signed URLs** con expiración para seguridad
- Compresión de PDFs al momento de subida
- Generación de thumbnails y previews automáticos

**Frontend (CDN)**:
- Servir archivos a través de **CloudFront** o **Cloudflare CDN**
- Aprovechar edge locations para latencia mínima global
- Cache agresivo con invalidación selectiva
- Streaming de PDFs (cargar solo las páginas visibles)

**Código ejemplo**:
```javascript
// Carga progresiva de páginas
const loadPage = async (pageNumber) => {
  const url = `${CDN_URL}/documents/${documentId}/page-${pageNumber}.json`;
  return await cachedFetch(url);
};
```

**Beneficio**: Reducción dramática en tiempos de carga, menor costo de servidor, mejor escalabilidad.

---

### 4. **Lazy Loading de Componentes y Code Splitting**

**Contexto**: Actualmente todos los componentes se cargan al inicio, incluso si el usuario no los usa. Esto aumenta el tiempo de carga inicial innecesariamente.

**Implementación sugerida**:
```javascript
// App.vue - Carga diferida del DocumentViewer
const DocumentViewer = defineAsyncComponent({
  loader: () => import('@/features/document-viewer'),
  loadingComponent: LoadingSpinner,
  delay: 200,
  errorComponent: ErrorComponent,
  timeout: 3000
});

// Lazy loading de features completas
const features = {
  search: () => import('@/features/search'),
  viewer: () => import('@/features/document-viewer'),
  annotations: () => import('@/features/annotations'), // futura
};
```

**Estrategia de code splitting**:
- Separar vendor bundles (Vue, librerías) del código de aplicación
- Prefetch de componentes probables (cuando usuario mueve mouse hacia botón de búsqueda)
- Preload de recursos críticos
- Lazy loading de iconos y assets pesados

**Beneficio**: Tiempo de carga inicial reducido en ~40-60%, mejor puntuación en Lighthouse, mejor percepción de velocidad.

---

### 5. **Modos de Visualización y Preferencias de Usuario**

**Contexto**: Diferentes usuarios tienen diferentes preferencias de lectura. Ofrecer opciones mejora la experiencia.

**Implementación sugerida**:

**Dark Mode**:
```javascript
// composables/useTheme.js
export const useTheme = () => {
  const isDark = useLocalStorage('theme-dark', false);

  const toggleTheme = () => {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle('dark');
  };

  return { isDark, toggleTheme };
};
```

**Vistas de Documentos**:
- **Vista de Lectura**: Actual, enfocada en un solo documento
- **Vista de Lista**: Tabla con títulos, autores, fechas, tamaño
- **Vista de Galería**: Grid con portadas y metadata
- **Vista Comparativa**: Ver dos PDFs lado a lado

**Preferencias guardadas**:
- Nivel de zoom preferido
- Página donde quedó la última lectura
- Tema (claro/oscuro)
- Vista preferida (lista/galería/lectura)
- Historial de documentos visitados

**Beneficio**: Personalización que mejora retención de usuarios y satisfacción.

---

### 6. **Mejora en el Sistema de Búsqueda (Backend)**

**Contexto actual**: La búsqueda depende de un archivo JSON pre-generado que mapea texto a posiciones. Esto tiene limitaciones:
- Requiere mantenimiento manual del JSON
- No soporta búsquedas dinámicas complejas
- Limitado a lo que está indexado en el JSON

**Implementación sugerida**:

Integrar una librería PHP especializada que permita:
- Extraer contenido del PDF de forma dinámica
- Obtener posiciones de texto sin depender de archivos estáticos
- Realizar búsquedas directamente sobre el contenido del documento
- Generar índices automáticamente según sea necesario

**Beneficio**: Mayor flexibilidad y mantenimiento simplificado al eliminar la dependencia de archivos JSON estáticos.

---

### 7. **Pruebas Unitarias y de Integración**

**Contexto**: Debido a limitaciones de tiempo en el ejercicio, las pruebas son mínimas. En producción, esto es **inaceptable** y debe ser prioridad #1.

**Implementación sugerida**:

**Tests Unitarios (Vitest + Vue Test Utils)**:
```javascript
// tests/unit/composables/useDocumentZoom.test.js
import { describe, it, expect } from 'vitest';
import { useDocumentZoom } from '@/features/document-viewer';

describe('useDocumentZoom', () => {
  it('debería inicializar con zoom 100%', () => {
    const { scale, zoomText } = useDocumentZoom();
    expect(scale.value).toBe(1);
    expect(zoomText.value).toBe('100%');
  });

  it('debería incrementar zoom correctamente', () => {
    const { scale, zoomIn } = useDocumentZoom();
    zoomIn();
    expect(scale.value).toBe(1.25);
  });

  it('no debería superar el zoom máximo', () => {
    const { scale, zoomIn } = useDocumentZoom();
    for(let i = 0; i < 10; i++) zoomIn(); // Intentar zoom excesivo
    expect(scale.value).toBeLessThanOrEqual(3);
  });
});
```

**Estrategia de testing**:
- **Coverage objetivo**: Mínimo 80% en código crítico (composables, utils)
- **CI/CD Integration**: Tests automáticos en cada PR
- **Visual Regression**: Capturar screenshots y detectar cambios visuales
- **Performance testing**: Medir tiempo de renderizado de PDFs grandes

**Beneficio**: Confianza en refactorings, prevención de regresiones, documentación viva del comportamiento esperado.

---

### 8. **Búsqueda Avanzada y Filtros**

**Contexto**: La búsqueda actual es simple: texto completo sin filtros. Usuarios avanzados necesitan más control.

**Implementación sugerida**:

**Interfaz de búsqueda avanzada**:
```vue
<template>
  <div class="advanced-search">
    <input v-model="query" placeholder="Buscar...">

    <select v-model="searchMode">
      <option value="contains">Contiene</option>
      <option value="exact">Frase exacta</option>
      <option value="fuzzy">Búsqueda aproximada</option>
      <option value="regex">Expresión regular</option>
    </select>

    <div class="filters">
      <DateRangePicker v-model="dateRange" label="Fecha documento" />
      <MultiSelect v-model="authors" :options="availableAuthors" />
      <MultiSelect v-model="categories" :options="availableCategories" />
      <Checkbox v-model="caseSensitive" label="Sensible a mayúsculas" />
    </div>
  </div>
</template>
```

**Filtros avanzados**:
- Por metadata del PDF (autor, fecha creación, tamaño)
- Por número de resultados (documentos con >10 coincidencias)
- Por sección del documento (solo buscar en capítulo específico)
- Por tipo de contenido (solo en títulos, solo en párrafos)

**Beneficio**: Herramienta profesional para investigadores, académicos y usuarios avanzados.

---

### 9. **Internacionalización (i18n)**

**Preparar la aplicación para múltiples idiomas**:
```javascript
// i18n/es.json
{
  "search.placeholder": "Buscar en el documento...",
  "search.results": "{count} resultado(s) encontrado(s)",
  "nav.next": "Siguiente",
  "nav.previous": "Anterior"
}

// i18n/en.json
{
  "search.placeholder": "Search in document...",
  "search.results": "{count} result(s) found",
  "nav.next": "Next",
  "nav.previous": "Previous"
}
```

**Beneficio**: Alcance global de la aplicación.

## Migración de Estructura Antigua

Estructura antigua:
```
components/
├── DocumentViewer.vue
└── SearchInput.vue
```

Nueva estructura:
```
features/
├── document-viewer/
│   ├── components/
│   │   └── DocumentViewer.vue
│   └── composables/
│       └── ...
└── search/
    ├── components/
    │   └── SearchInput.vue
    └── composables/
        └── ...
```

## Recursos Adicionales

- [Vertical Slice Architecture](https://www.jimmybogard.com/vertical-slice-architecture/)
- [Vue 3 Composition API](https://vuejs.org/guide/extras/composition-api-faq.html)
- [Vue Composables Best Practices](https://vuejs.org/guide/reusability/composables.html)
- [Tato30/vue-pdf Documentation](https://github.com/TaTo30/VuePDF)

## Conclusión

Este proyecto implementa un buscador de contenido en PDFs con una arquitectura pensada para el crecimiento y la escalabilidad. La elección de Vertical Slice Architecture, junto con Vue.js y Tailwind CSS, proporciona una base sólida para futuras expansiones y mantenimiento del código.

La decisión de usar herramientas familiares para el equipo (Vue.js + Tailwind) y librerías bien documentadas (tato30/vue-pdf) permitió un desarrollo eficiente enfocado en la funcionalidad principal: **un buscador claro y fácil de usar**.