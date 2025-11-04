import { ref, watch } from "vue";
import { debounce } from "../../../utils/debounce";

const DEBOUNCE_DELAY = 500;

/**
 * @summary Composable for managing search input functionality
 * @description Handles search text, loading state, and debounced search execution
 * @param {Object} props - Component props
 * @param {Function} emit - Emit function from component
 * @returns {Object} Search state and methods
 */
export const useSearch = (props, emit) => {
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

  return {
    searchText,
    isLoading,
    handleSearch,
    handleInput,
    handleClear,
  };
};
