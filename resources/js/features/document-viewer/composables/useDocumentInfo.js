import { ref } from "vue";

/**
 * @summary Composable for managing document information
 * @description Handles fetching and storing document metadata from the API
 * @returns {Object} Document info state and methods
 */
export const useDocumentInfo = () => {
  const pdfSource = ref(null);
  const documentName = ref("Loading...");

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

  return {
    pdfSource,
    documentName,
    fetchDocumentInfo,
  };
};
