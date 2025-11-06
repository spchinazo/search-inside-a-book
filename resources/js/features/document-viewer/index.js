/**
 * Document Viewer Feature
 *
 * This vertical slice contains all the functionality related to viewing PDF documents,
 * including navigation, zoom, and search integration.
 */

// Components
export { default as DocumentViewer } from "./components/DocumentViewer.vue";

// Composables
export { useDocumentInfo } from "./composables/useDocumentInfo";
export { useDocumentSearch } from "./composables/useDocumentSearch";
export { useDocumentNavigation } from "./composables/useDocumentNavigation";
export { useDocumentZoom } from "./composables/useDocumentZoom";
