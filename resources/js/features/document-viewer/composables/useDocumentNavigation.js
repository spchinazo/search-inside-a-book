import { ref } from "vue";

/**
 * @summary Composable for managing document page navigation
 * @description Handles current page, total pages, and navigation methods
 * @returns {Object} Navigation state and methods
 */
export const useDocumentNavigation = () => {
  const currentPage = ref(1);
  const totalPages = ref(0);

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
   * @param {Object} pages - Pages object from usePDF composable
   */
  const handleDocumentLoad = (pdfInfo, pages) => {
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
   * @summary Resets page to first page
   * @description Sets current page back to 1
   */
  const resetPage = () => {
    currentPage.value = 1;
  };

  return {
    currentPage,
    totalPages,
    goToPage,
    handleDocumentLoad,
    nextPage,
    previousPage,
    resetPage,
  };
};
