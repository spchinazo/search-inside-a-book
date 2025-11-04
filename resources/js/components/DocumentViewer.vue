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
            <ListBulletIcon class="w-6 h-6 cursor-pointer" />
            <Squares2X2Icon class="w-6 h-6 cursor-pointer" />
          </div>

          <div class="bg-gray-200 h-10 w-[1px]" />

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

          <div class="bg-gray-200 h-10 w-[1px]" />

          <div class="flex items-center gap-6">
            <SunIcon class="w-6 h-6 cursor-pointer" />
            <MoonIcon class="w-6 h-6 cursor-pointer" />
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
            @loaded="handleDocumentLoad"
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

        <span class="text-sm font-medium text-gray-700">
          Page {{ currentPage }} of {{ totalPages }}
        </span>

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

    <div class="w-96 bg-white border-l border-gray-200 flex flex-col">
      <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800 truncate">
          {{ documentName }}
        </h2>

        <div class="flex items-center gap-5">
          <ArrowPathIcon @click="resetViewer" class="w-5 h-5 cursor-pointer" />
          <ShareIcon class="w-5 h-5 cursor-pointer text-blue-500" />
        </div>
      </div>

      <div class="flex-1 overflow-auto p-6">
        <div
          class="mb-6 flex flex-col space-y-5"
          :class="{ 'h-full': searchQuery.length === 0 && searchResults.length === 0 }"
        >
          <p class="block text-center text-lg text-gray-900 mb-2">Search in Document</p>

          <SearchInput
            v-model="searchQuery"
            :is-loading="isSearching"
            @search="performSearch"
            @clear="clearSearch"
          />

          <div
            v-if="searchQuery.length === 0 && searchResults.length === 0"
            class="grow flex flex-col items-center justify-center"
          >
            <DocumentMagnifyingGlassIcon class="w-20 h-20 text-gray-200 mb-5" />
            <p class="text-center mb-2">Explora el contenido</p>
            <p class="text-center">
              Escribe tu búsqueda y verás cada coincidencia claramente destacada.
            </p>
          </div>

          <div
            v-if="showNoResults"
            class="mt-3 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded"
          >
            <div class="flex">
              <div class="flex-shrink-0">
                <ExclamationTriangleIcon class="h-5 w-5 text-yellow-400" />
              </div>

              <div class="ml-3">
                <p class="text-sm text-yellow-700">
                  No results found for "{{ searchQuery }}". Try a different search term.
                </p>
              </div>
            </div>
          </div>
        </div>

        <div v-if="searchResults.length > 0" class="space-y-4">
          <div class="text-sm font-medium text-gray-700 mb-3">
            Found {{ searchResults.length }} match{{
              searchResults.length !== 1 ? "es" : ""
            }}
          </div>

          <div
            v-for="(result, index) in searchResults"
            :key="index"
            @click="goToPage(result.location.value)"
            class="p-4 bg-gray-50 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 cursor-pointer transition-colors"
          >
            <div class="flex items-start justify-between mb-2">
              <span class="text-xs font-semibold text-blue-600"
                >Page {{ result.location.value }}</span
              >
            </div>
            <p class="text-sm text-gray-700" v-html="result.snippet"></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  DocumentMagnifyingGlassIcon,
  MagnifyingGlassMinusIcon,
  MagnifyingGlassPlusIcon,
  ExclamationTriangleIcon,
  ChevronRightIcon,
  ChevronLeftIcon,
  ListBulletIcon,
  ArrowPathIcon,
  ShareIcon,
} from "@heroicons/vue/24/solid";
import {
  ComputerDesktopIcon,
  Squares2X2Icon,
  MoonIcon,
  SunIcon,
} from "@heroicons/vue/24/outline";

import { VuePDF, usePDF } from "@tato30/vue-pdf";
import SearchInput from "./SearchInput.vue";
import { debounce } from "../utils/debounce";
import { ref, onMounted } from "vue";

import "@tato30/vue-pdf/style.css";

const HIGHLIGHT_OPTIONS = { completeWords: false, ignoreCase: false };
const MAX_ZOOM_PERCENTAGE = 300;
const MIN_ZOOM_PERCENTAGE = 50;
const DEBOUNCE_DELAY = 500;
const ZOOM_STEP = 0.25;
const DEFAULT_ZOOM = 2;
const MIN_ZOOM = 0.5;
const MAX_ZOOM = 3;

const pdfSource = ref(null);
const currentPage = ref(1);
const totalPages = ref(0);
const documentName = ref("Loading...");
const searchQuery = ref("");
const searchResults = ref([]);
const showNoResults = ref(false);
const isSearching = ref(false);

const highlightText = ref("");
const highlightOptions = ref(HIGHLIGHT_OPTIONS);

const zoomText = ref(`${DEFAULT_ZOOM * 100}%`);
const scale = ref(DEFAULT_ZOOM);

const { pdf, pages, info } = usePDF(pdfSource);

/**
 * @summary Fetches document information from the API
 * @description Retrieves the document filename and path from the server and updates the component state with this information
 * @returns {Promise<void>}
 */
const fetchDocumentInfo = async () => {
  try {
    const response = await fetch("/api/documents/info");
    const data = await response.json();

    documentName.value = data.filename;
    pdfSource.value = data.path;
  } catch (error) {
    console.error("Error fetching document info:", error);
    documentName.value = "Document";
  }
};

/**
 * @summary Performs a search query on the document
 * @description Sends a search request to the API with the current search query and updates the search results, highlighting matches in the PDF
 * @returns {Promise<void>}
 */
const performSearch = async () => {
  if (!searchQuery.value.trim()) {
    searchResults.value = [];
    showNoResults.value = false;
    highlightText.value = "";
    return;
  }

  isSearching.value = true;

  try {
    const response = await fetch(
      `/api/documents/search?query=${encodeURIComponent(searchQuery.value)}`
    );
    const data = await response.json();

    searchResults.value = data;
    showNoResults.value = data.length === 0;

    highlightText.value = searchQuery.value;
  } catch (error) {
    console.error("Error searching document:", error);
    searchResults.value = [];
    showNoResults.value = true;
    highlightText.value = "";
  } finally {
    isSearching.value = false;
  }
};

/**
 * @summary Clears the search results
 * @description Resets search query, results, and highlight when user clears the search input
 */
const clearSearch = () => {
  searchQuery.value = "";
  searchResults.value = [];
  showNoResults.value = false;
  highlightText.value = "";
};

/**
 * @summary Navigates to a specific page in the PDF
 * @description Updates the current page number to display the specified page in the PDF viewer
 * @param {number} pageNumber - The page number to navigate to
 */
const goToPage = (pageNumber) => {
  currentPage.value = pageNumber;
};

/**
 * @summary Handles PDF document load event
 * @description Extracts and sets the total number of pages from the PDF information object
 * @param {Object} pdfInfo - PDF information object containing page count
 */
const handleDocumentLoad = (pdfInfo) => {
  totalPages.value = pages.value || pdfInfo.pages || pdfInfo.numPages || pdfInfo;
};

/**
 * @summary Navigates to the next page
 * @description Increments the current page number if not already at the last page
 */
const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

/**
 * @summary Navigates to the previous page
 * @description Decrements the current page number if not already at the first page
 */
const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

/**
 * @summary Increases the PDF zoom level
 * @description Increases the scale by the defined step amount up to the maximum zoom level and updates the zoom text display
 */
const zoomIn = () => {
  scale.value = Math.min(scale.value + ZOOM_STEP, MAX_ZOOM);
  zoomText.value = Math.round(scale.value * 100) + "%";
};

/**
 * @summary Decreases the PDF zoom level
 * @description Decreases the scale by the defined step amount down to the minimum zoom level and updates the zoom text display
 */
const zoomOut = () => {
  scale.value = Math.max(scale.value - ZOOM_STEP, MIN_ZOOM);
  zoomText.value = Math.round(scale.value * 100) + "%";
};

/**
 * @summary Updates zoom level from text input
 * @description Extracts a numeric value from text input using regex, clamps it within allowed zoom range, and updates the scale
 * @param {string} text - The text input containing the zoom percentage value
 */
const updateZoomFromText = (text) => {
  const numberMatch = text.match(/\d+/);

  if (!numberMatch) {
    return;
  }

  const zoomPercentage = parseInt(numberMatch[0], 10);
  const clampedZoom = Math.max(
    MIN_ZOOM_PERCENTAGE,
    Math.min(MAX_ZOOM_PERCENTAGE, zoomPercentage)
  );
  const newScale = clampedZoom / 100;

  scale.value = newScale;
};

/**
 * @summary Debounced version of updateZoomFromText
 * @description Creates a debounced function that delays zoom update execution by the specified delay time
 * @returns {Function}
 */
const debouncedZoomUpdate = debounce(updateZoomFromText, DEBOUNCE_DELAY);

/**
 * @summary Handles zoom input events
 * @description Extracts the input value and triggers the debounced zoom update function
 * @param {Event} event - The input event object
 */
const handleZoomInput = (event) => {
  const inputValue = event.target.value;
  debouncedZoomUpdate(inputValue);
};

/**
 * @summary Normalizes the zoom text display
 * @description Updates the zoom text to display the current scale as a rounded percentage with the % symbol
 */
const normalizeZoomText = () => {
  zoomText.value = Math.round(scale.value * 100) + "%";
};

/**
 * @summary Resets the viewer to default state
 * @description Resets zoom level to default, clears search query, results, and highlights
 */
const resetViewer = () => {
  scale.value = DEFAULT_ZOOM;
  zoomText.value = Math.round(DEFAULT_ZOOM * 100) + "%";
  searchQuery.value = "";
  searchResults.value = [];
  showNoResults.value = false;
  highlightText.value = "";
  currentPage.value = 1;
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
