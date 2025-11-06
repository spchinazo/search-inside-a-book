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
import {
  MagnifyingGlassIcon,
  ArrowPathIcon,
  XMarkIcon,
} from "@heroicons/vue/24/solid";
import { useSearch } from "../composables/useSearch";

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

const {
  searchText,
  isLoading,
  handleSearch,
  handleInput,
  handleClear,
} = useSearch(props, emit);
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
