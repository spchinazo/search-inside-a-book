<template>
  <div
    :class="[
      'w-96 bg-white border-l border-gray-200 flex flex-col shadow-2xl md:shadow-none',
      'fixed sm:fixed md:fixed lg:fixed xl:relative 2xl:relative right-0 top-0 h-full z-50 md:z-auto',
      'transition-all duration-300 ease-out',
      isOpen ? 'translate-x-0' : 'translate-x-full xl:translate-x-0 2xl:translate-x-0'
    ]"
  >
      <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800 truncate">
          {{ documentName }}
        </h2>

        <div class="flex items-center gap-5">
          <ArrowPathIcon @click="handleReset" class="w-5 h-5 cursor-pointer" />
          <XMarkIcon
            @click="handleClose"
            class="w-5 h-5 cursor-pointer md:hidden"
          />
        </div>
      </div>

      <div class="flex-1 overflow-auto p-6">
        <div
          class="mb-6 flex flex-col space-y-5"
          :class="{ 'h-full': searchQuery.length === 0 && searchResults.length === 0 }"
        >
          <p class="block text-center text-lg text-gray-900 mb-2">Search in Document</p>

          <SearchInput
            :model-value="searchQuery"
            @update:model-value="handleUpdateQuery"
            :is-loading="isSearching"
            @search="handleSearch"
            @clear="handleClear"
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
            @click="handleGoToPage(result.location.value)"
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
</template>

<script setup>
import { ref, computed } from "vue";
import {
  DocumentMagnifyingGlassIcon,
  ExclamationTriangleIcon,
  ArrowPathIcon,
  ShareIcon,
  XMarkIcon,
} from "@heroicons/vue/24/solid";

import SearchInput from "./SearchInput.vue";

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
  documentName: {
    type: String,
    required: true,
  },
  searchQuery: {
    type: String,
    required: true,
  },
  searchResults: {
    type: Array,
    required: true,
  },
  showNoResults: {
    type: Boolean,
    required: true,
  },
  isSearching: {
    type: Boolean,
    required: true,
  },
});

const emit = defineEmits([
  "update:searchQuery",
  "search",
  "clear",
  "reset",
  "goToPage",
  "close",
]);

/**
 * @summary Handles search query update
 * @description Emits update:searchQuery event to parent component when search text changes
 * @param {string} value - The new search query value
 */
const handleUpdateQuery = (value) => {
  emit("update:searchQuery", value);
};

/**
 * @summary Handles search event
 * @description Emits search event to parent component
 */
const handleSearch = () => {
  emit("search");
};

/**
 * @summary Handles clear search event
 * @description Emits clear event to parent component
 */
const handleClear = () => {
  emit("clear");
};

/**
 * @summary Handles reset event
 * @description Emits reset event to parent component
 */
const handleReset = () => {
  emit("reset");
};

/**
 * @summary Handles navigation to specific page
 * @description Emits goToPage event with page number and closes panel
 * @param {number} pageNumber - The page number to navigate to
 */
const handleGoToPage = (pageNumber) => {
  emit("goToPage", pageNumber);
  handleClose();
};

/**
 * @summary Handles closing the search panel
 * @description Emits close event to parent component
 */
const handleClose = () => {
  emit("close");
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
