import { ref } from "vue";

const HIGHLIGHT_OPTIONS = { completeWords: false, ignoreCase: false };

/**
 * @summary Composable for managing document search functionality
 * @description Handles search queries, results, and text highlighting in the PDF
 * @returns {Object} Search state and methods
 */
export const useDocumentSearch = () => {
  const searchQuery = ref("");
  const searchResults = ref([]);
  const showNoResults = ref(false);
  const isSearching = ref(false);
  const highlightText = ref("");
  const highlightOptions = ref(HIGHLIGHT_OPTIONS);

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

  return {
    searchQuery,
    searchResults,
    showNoResults,
    isSearching,
    highlightText,
    highlightOptions,
    performSearch,
    clearSearch,
  };
};
