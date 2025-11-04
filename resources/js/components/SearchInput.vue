<template>
  <div class="w-full relative">
    <input
      v-model="searchText"
      @input="handleInput"
      type="text"
      placeholder="Enter search term..."
      class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
    />

    <button v-if="isLoading" class="absolute right-2 top-2 p-1 cursor-default" disabled>
      <ArrowPathIcon class="w-5 h-5 text-gray-500 animate-spin" />
    </button>

    <button
      v-else-if="searchText.length > 0"
      @click="handleClear"
      class="absolute right-2 top-2 p-1 hover:bg-gray-100 rounded"
    >
      <XMarkIcon class="w-5 h-5 text-gray-500" />
    </button>

    <button
      v-else
      @click="handleSearch"
      class="absolute right-2 top-2 p-1 hover:bg-gray-100 rounded"
    >
      <MagnifyingGlassIcon class="w-5 h-5 text-gray-500" />
    </button>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";
import {
  MagnifyingGlassIcon,
  ArrowPathIcon,
  XMarkIcon,
} from "@heroicons/vue/24/solid";
import { debounce } from "../utils/debounce";

const DEBOUNCE_DELAY = 500;

const props = defineProps({
  modelValue: {
    type: String,
    default: "",
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["update:modelValue", "search", "clear"]);

const searchText = ref(props.modelValue);
const isLoading = ref(props.isLoading);

/**
 * @summary Triggers the search event
 * @description Emits the search event to notify parent component to perform search
 */
const handleSearch = () => {
  emit("search");
};

/**
 * @summary Debounced search trigger
 * @description Creates a debounced function that delays search execution by the specified delay time
 * @returns {Function}
 */
const debouncedSearch = debounce(handleSearch, DEBOUNCE_DELAY);

/**
 * @summary Handles search input changes
 * @description Updates the model value and triggers debounced search when user types
 * @param {Event} event - The input event object
 */
const handleInput = (event) => {
  const inputValue = event.target.value;
  searchText.value = inputValue;
  emit("update:modelValue", inputValue);

  if (inputValue.trim().length > 0) {
    debouncedSearch();
  }
};

/**
 * @summary Clears the search input
 * @description Resets the search query and emits clear event to parent component
 */
const handleClear = () => {
  searchText.value = "";
  emit("update:modelValue", "");
  emit("clear");
};

watch(
  () => props.modelValue,
  (newValue) => {
    searchText.value = newValue;
  }
);

watch(
  () => props.isLoading,
  (newValue) => {
    isLoading.value = newValue;
  }
);
</script>

<style scoped>
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
