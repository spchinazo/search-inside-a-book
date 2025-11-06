<template>
  <div class="flex h-screen bg-gray-50">
    <!-- Left Panel - PDF Viewer -->
    <div class="flex-1 flex flex-col bg-white">
      <!-- Top Navigation Bar -->
      <div
        class="border-b border-gray-200 px-6 py-4 flex items-center justify-between bg-white"
      >
        <button
          @click="goToLibrary"
          class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors cursor-pointer"
        >
          <ChevronLeftIcon class="w-5 h-5" />
          <span class="font-medium">Back</span>
        </button>

        <div class="flex items-center gap-10">
          <div class="flex items-center gap-6">
            <MagnifyingGlassPlusIcon @click="zoomIn" class="w-6 h-6 cursor-pointer" />
            <input
              type="text"
              class="w-20 h-8 border border-gray-300 shadow-xs rounded px-1 text-center"
              v-model="zoomText"
              @input="handleZoomInput"
              @blur="normalizeZoomText"
            />
            <MagnifyingGlassMinusIcon @click="zoomOut" class="w-6 h-6 cursor-pointer" />
          </div>

          <div class="flex items-center gap-6">
            <MagnifyingGlassIcon
              @click="toggleSearchPanel"
              class="w-6 h-6 cursor-pointer block sm:block md:block lg:block xl:hidden 2xl:hidden hover:text-blue-600 transition-colors"
              :class="{ 'text-blue-600': isSearchPanelOpen }"
            />
          </div>
        </div>

        <div class="flex items-center gap-3"></div>
      </div>

      <div class="flex-1 overflow-auto bg-gray-100 p-8 flex justify-center">
        <div v-if="pdf" class="bg-white shadow-lg">
          <VuePDF
            :pdf="pdf"
            :page="currentPage"
            :scale="scale"
            text-layer
            :highlight-text="highlightText"
            :highlight-options="highlightOptions"
            @loaded="handleLoad"
          />
        </div>
      </div>

      <!-- Bottom Navigation -->
      <div
        class="border-t border-gray-200 px-6 py-4 bg-white flex items-center justify-center gap-4"
      >
        <button
          @click="previousPage"
          :disabled="currentPage <= 1"
          :class="[
            'p-2 rounded-lg transition-all',
            currentPage <= 1
              ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
              : 'bg-blue-600 text-white hover:bg-blue-700',
          ]"
        >
          <ChevronLeftIcon class="w-5 h-5" />
        </button>

        <div class="flex items-center gap-2">
          <span class="text-sm font-medium text-gray-700">Page</span>
          <input
            type="number"
            v-model="manualPageInput"
            @input="handleManualPageInput"
            :min="1"
            :max="totalPages"
            class="w-16 px-2 py-1 text-sm font-medium text-gray-700 text-center border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
          <span class="text-sm font-medium text-gray-700">of {{ totalPages }}</span>
        </div>

        <button
          @click="nextPage"
          :disabled="currentPage >= totalPages"
          :class="[
            'p-2 rounded-lg transition-all',
            currentPage >= totalPages
              ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
              : 'bg-blue-600 text-white hover:bg-blue-700',
          ]"
        >
          <ChevronRightIcon class="w-5 h-5" />
        </button>
      </div>
    </div>

    <!-- Search Panel -->
    <SearchPanel
      :is-open="isSearchPanelOpen"
      :document-name="documentName"
      :search-query="searchQuery"
      :search-results="searchResults"
      :show-no-results="showNoResults"
      :is-searching="isSearching"
      @update:search-query="(value) => (searchQuery = value)"
      @search="performSearch"
      @clear="clearSearch"
      @reset="resetViewer"
      @go-to-page="goToPage"
      @close="closeSearchPanel"
    />
  </div>
</template>

<script setup>
import {
  MagnifyingGlassMinusIcon,
  MagnifyingGlassPlusIcon,
  MagnifyingGlassIcon,
  ChevronRightIcon,
  ChevronLeftIcon,
  ListBulletIcon,
} from "@heroicons/vue/24/solid";
import {
  ComputerDesktopIcon,
  Squares2X2Icon,
} from "@heroicons/vue/24/outline";

import { VuePDF, usePDF } from "@tato30/vue-pdf";
import { SearchPanel } from "../../search";
import { onMounted, ref, watch } from "vue";

import { useDocumentInfo } from "../composables/useDocumentInfo";
import { useDocumentSearch } from "../composables/useDocumentSearch";
import { useDocumentNavigation } from "../composables/useDocumentNavigation";
import { useDocumentZoom } from "../composables/useDocumentZoom";
import { useSearchPanel } from "../../search/composables/useSearchPanel";
import { debounce } from "../../../utils/debounce";

import "@tato30/vue-pdf/style.css";

const { pdfSource, documentName, fetchDocumentInfo } = useDocumentInfo();

const {
  searchQuery,
  searchResults,
  showNoResults,
  isSearching,
  highlightText,
  highlightOptions,
  performSearch,
  clearSearch,
} = useDocumentSearch();

const {
  currentPage,
  totalPages,
  goToPage,
  handleDocumentLoad,
  nextPage,
  previousPage,
  resetPage,
} = useDocumentNavigation();

const {
  zoomText,
  scale,
  zoomIn,
  zoomOut,
  handleZoomInput,
  normalizeZoomText,
  resetZoom,
} = useDocumentZoom();

const { isSearchPanelOpen, toggleSearchPanel, closeSearchPanel } =
  useSearchPanel();

const { pdf, pages } = usePDF(pdfSource);

const manualPageInput = ref("1");

/**
 * @summary Handles PDF document load event
 * @description Wrapper function to pass pages ref to navigation composable
 * @param {Object} pdfInfo - PDF information object containing page count
 */
const handleLoad = (pdfInfo) => {
  handleDocumentLoad(pdfInfo, pages);
};

/**
 * @summary Handles manual page input changes
 * @description Validates and navigates to the page entered by user with debounce
 * @param {Event} event - Input event object
 */
const handleManualPageInput = debounce((event) => {
  const pageNumber = parseInt(event.target.value, 10);

  if (isNaN(pageNumber)) {
    return;
  }

  if (pageNumber < 1 || pageNumber > totalPages.value) {
    manualPageInput.value = currentPage.value.toString();
    return;
  }

  goToPage(pageNumber);
}, 500);

/**
 * @summary Syncs manual input with current page
 * @description Updates the input field when page changes via navigation buttons
 */
watch(currentPage, (newPage) => {
  manualPageInput.value = newPage.toString();
});

/**
 * @summary Resets the viewer to default state
 * @description Resets zoom level to default, clears search query, results, highlights, and page
 */
const resetViewer = () => {
  manualPageInput.value = "1";
  resetZoom();
  clearSearch();
  resetPage();
};

/**
 * @summary Redirects to the library page
 * @description Navigates the browser to the Aleph Digital library URL
 */
const goToLibrary = () => {
  window.location.href = "https://alephdigital.publica.la/library";
};

onMounted(() => {
  fetchDocumentInfo();
});
</script>

<style scoped>
/* Tailwind handles all styling */
</style>
