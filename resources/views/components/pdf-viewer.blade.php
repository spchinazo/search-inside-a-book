<div x-data="pdfViewer()" x-init="init()" class="mt-8">
    {{-- Contenedor PDF --}}
    <div x-show="isVisible" class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Página <span x-text="currentPage"></span></h2>
            <button @click="close()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        {{-- Estado de Carga --}}
        <div x-show="isLoading" class="flex justify-center items-center py-12">
            <svg class="animate-spin h-10 w-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
        </div>

        {{-- Canvas PDF --}}
        <div class="overflow-auto max-h-150">
            <canvas id="pdf-canvas" class="mx-auto"></canvas>
        </div>

        {{-- Controles de Navegación --}}
        <div x-show="!isLoading" class="flex justify-center items-center space-x-4 mt-4">
            <button @click="previousPage()" :disabled="currentPage <= 1"
                class="px-4 py-2 bg-blue-500 text-white rounded disabled:opacity-50 disabled:cursor-not-allowed">
                Anterior
            </button>
            <span class="text-gray-600">
                Página <span x-text="currentPage"></span> de <span x-text="totalPages"></span>
            </span>
            <button @click="nextPage()" :disabled="currentPage >= totalPages"
                class="px-4 py-2 bg-blue-500 text-white rounded disabled:opacity-50 disabled:cursor-not-allowed">
                Siguiente
            </button>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/5.4.149/pdf.min.mjs" type="module"></script>
<script>
    // Store PDF document outside Alpine's reactive system to avoid proxy issues
    let pdfDocument = null;

    function pdfViewer() {
        return {
            isVisible: false,
            isLoading: false,
            currentPage: 1,
            totalPages: 0,
            searchQuery: '',

            init() {
                // Configuramos el worker de PDF.js
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/5.4.149/pdf.worker.min.mjs';

                // Escuchamos el evento de selección de una página desde Livewire
                window.addEventListener('page-selected', (event) => { this.loadPage(event.detail.pageNumber) });

                // Obtener query de búsqueda desde la URL para resaltar
                const urlParams = new URLSearchParams(window.location.search);
                this.searchQuery = urlParams.get('q') || '';
            },

            async loadPage(pageNumber) {
                this.isVisible = true;
                this.isLoading = true;
                this.currentPage = pageNumber;

                if (!pdfDocument) {
                    await this.loadPdf();
                }

                await this.renderPage(pageNumber);
                this.isLoading = false;
            },

            async loadPdf() {
                const pdfUrl = 'books/Eloquent_JavaScript.pdf';
                pdfDocument = await pdfjsLib.getDocument(pdfUrl).promise;
                this.totalPages = pdfDocument.numPages;
            },

            async renderPage(pageNumber) {
                const page = await pdfDocument.getPage(pageNumber);
                const canvas = document.getElementById('pdf-canvas');
                const context = canvas.getContext('2d');

                // Establecer escala para buena calidad
                const scale = 1.5;
                const viewport = page.getViewport({ scale: scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                // Renderiza la página PDF
                await page.render({
                    canvasContext: context,
                    viewport: viewport
                }).promise;
                // Si tenemos una consulta de búsqueda, intentar resaltarla
                // if (this.searchQuery) {
                //     await this.highlightSearchTerm(page, viewport);
                // }
            },

              async highlightSearchTerm(page, viewport) {
            // Obtener contenido de texto de la página
            const textContent = await page.getTextContent();
            const canvas = document.getElementById('pdf-canvas');
            const context = canvas.getContext('2d');

            // Buscar el término en elementos de texto
            textContent.items.forEach(item => {
                if (item.str.toLowerCase().includes(this.searchQuery.toLowerCase())) {
                    // Transformar coordenadas de PDF a canvas
                    const transform = pdfjsLib.Util.transform(
                        viewport.transform,
                        item.transform
                    );

                    // Dibujar rectángulo de resaltado
                    context.fillStyle = 'rgba(255, 255, 0, 0.3)';
                    context.fillRect(
                        transform[4],
                        canvas.height - transform[5] - item.height,
                        item.width,
                        item.height
                    );
                }
            });
        },

        async nextPage() {
            if (this.currentPage < this.totalPages) {
                await this.loadPage(this.currentPage + 1);
            }
        },

        async previousPage() {
            if (this.currentPage > 1) {
                await this.loadPage(this.currentPage - 1);
            }
        },

        close() {}
        };
    }
</script>
