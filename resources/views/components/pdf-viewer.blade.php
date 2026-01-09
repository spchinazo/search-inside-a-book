<div x-data="pdfViewer()" x-init="init()" class="mt-8">
    {{-- Contenedor PDF --}}
    <div x-show="isVisible" class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Página <span x-text="currentPage"></span></h2>
            <button
                @click="close()"
                class="text-gray-500 hover:text-gray-700"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Estado de Carga --}}
        <div x-show="isLoading" class="flex justify-center items-center py-12">
            <svg class="animate-spin h-10 w-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        {{-- Canvas PDF --}}
        <div class="overflow-auto max-h-150">
            <canvas id="pdf-canvas" class="mx-auto"></canvas>
        </div>

        {{-- Controles de Navegación --}}
        <div x-show="!isLoading" class="flex justify-center items-center space-x-4 mt-4">
            <button
                @click="previousPage()"
                :disabled="currentPage <= 1"
                class="px-4 py-2 bg-blue-500 text-white rounded disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Anterior
            </button>
            <span class="text-gray-600">
                Página <span x-text="currentPage"></span> de <span x-text="totalPages"></span>
            </span>
            <button
                @click="nextPage()"
                :disabled="currentPage >= totalPages"
                class="px-4 py-2 bg-blue-500 text-white rounded disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Siguiente
            </button>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/5.4.149/pdf.min.mjs" type="module"></script>
<script>
    function pdfViewer() {
        console.log('PDF Viewer component loaded');
        return {
            isVisible: false,
            isLoading: false,
            currentPage: 1,
            totalPages: 0,
            pdfDoc: null,
            searchQuery: '',


        };
    }
</script>
