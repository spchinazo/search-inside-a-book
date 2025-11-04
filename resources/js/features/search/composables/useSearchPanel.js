import { ref } from "vue";

/**
 * @summary Composable for managing search panel visibility
 * @description Handles the open/close state and toggle functionality for the search panel
 * @returns {Object} Search panel state and methods
 */
export const useSearchPanel = () => {
  const isSearchPanelOpen = ref(false);

  /**
   * @summary Toggles the search panel visibility
   * @description Opens the panel if closed, closes it if open
   */
  const toggleSearchPanel = () => {
    isSearchPanelOpen.value = !isSearchPanelOpen.value;
  };

  /**
   * @summary Opens the search panel
   * @description Sets the panel state to open
   */
  const openSearchPanel = () => {
    isSearchPanelOpen.value = true;
  };

  /**
   * @summary Closes the search panel
   * @description Sets the panel state to closed
   */
  const closeSearchPanel = () => {
    isSearchPanelOpen.value = false;
  };

  return {
    isSearchPanelOpen,
    toggleSearchPanel,
    openSearchPanel,
    closeSearchPanel,
  };
};
