import { ref } from "vue";
import { debounce } from "../../../utils/debounce";

const MAX_ZOOM_PERCENTAGE = 300;
const MIN_ZOOM_PERCENTAGE = 50;
const DEBOUNCE_DELAY = 500;
const ZOOM_STEP = 0.25;
const DEFAULT_ZOOM = 2;
const MIN_ZOOM = 0.5;
const MAX_ZOOM = 3;

/**
 * @summary Composable for managing document zoom functionality
 * @description Handles zoom level, zoom text display, and zoom controls
 * @returns {Object} Zoom state and methods
 */
export const useDocumentZoom = () => {
  const zoomText = ref(`${DEFAULT_ZOOM * 100}%`);
  const scale = ref(DEFAULT_ZOOM);

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
   * @summary Resets zoom to default level
   * @description Sets scale and zoom text back to default values
   */
  const resetZoom = () => {
    scale.value = DEFAULT_ZOOM;
    zoomText.value = Math.round(DEFAULT_ZOOM * 100) + "%";
  };

  return {
    zoomText,
    scale,
    zoomIn,
    zoomOut,
    handleZoomInput,
    normalizeZoomText,
    resetZoom,
  };
};
