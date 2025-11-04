/**
 * Search Feature
 *
 * This vertical slice contains all the functionality related to search inputs,
 * including debouncing, loading states, clear functionality, and search panel.
 */

// Components
export { default as SearchInput } from "./components/SearchInput.vue";
export { default as SearchPanel } from "./components/SearchPanel.vue";

// Composables
export { useSearch } from "./composables/useSearch";
export { useSearchPanel } from "./composables/useSearchPanel";
