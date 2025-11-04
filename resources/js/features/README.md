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

### 1. **Responsive Design / Optimización Mobile**
Mejorar la experiencia en dispositivos móviles con:
- Controles táctiles optimizados
- Layout adaptativo para pantallas pequeñas
- Gestos para zoom y navegación

### 2. **Servicio de API Frontend**
Crear una clase centralizada para el control de peticiones HTTP desde el frontend:
- Interceptores para manejo de errores
- Cache de peticiones
- Manejo de tokens y autenticación
- Retry logic

### 3. **Optimización de Carga de Archivos (Backend)**
Implementar soluciones cloud para mejorar la rapidez:
- **AWS S3** o **Cloudflare R2** para almacenamiento de archivos
- **CloudFront** o **Cloudflare CDN** para distribución global
- Compresión y optimización de PDFs
- Carga bajo demanda (lazy loading) de páginas

### 4. **Lazy Loading de Componentes**
Implementar carga diferida para no saturar la vista:
```javascript
// Carga diferida de componentes pesados
const DocumentViewer = defineAsyncComponent(() =>
  import('@/features/document-viewer')
);
```

### 5. **Modos de Visualización**
Agregar opciones de experiencia de usuario:
- **Dark Mode**: Modo oscuro para lectura nocturna
- **Vista de Listado**: Ver títulos de documentos en lista
- **Vista de Galería**: Ver miniaturas de documentos
- Preferencias guardadas en localStorage

### 6. **Mejora en el Sistema de Búsqueda (Backend)**
Implementar una librería más robusta para el procesamiento de búsquedas:
- Librería capaz de leer el contenido del PDF directamente
- Extracción precisa de posiciones de texto sin depender de JSON
- Generación dinámica de índices
- Búsqueda con tolerancia a errores tipográficos
- Búsqueda por frases exactas y proximidad

### 7. **Pruebas Unitarias y de Integración**
Debido a limitaciones de tiempo, no se pudieron agregar pruebas unitarias completas. A futuro son **esenciales**:
- Tests unitarios para composables
- Tests de integración para features completos
- Tests E2E con Cypress o Playwright
- Coverage mínimo del 80%

### 8. **Sistema de Anotaciones**
- Permitir a usuarios agregar notas en los documentos
- Guardar highlights personalizados
- Exportar anotaciones

### 9. **Búsqueda Avanzada**
- Filtros por fecha, autor, categoría
- Búsqueda con operadores booleanos (AND, OR, NOT)
- Búsqueda por similitud semántica

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
