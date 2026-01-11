<div x-data="pdfViewer()" x-init="init()" class="h-full w-full bg-base-200 relative">
    {{-- Estado de Carga --}}
    <div x-show="isLoading" class="flex justify-center items-center h-full">
        <span class="loading loading-spinner loading-lg"></span>
    </div>

    {{-- Canvas PDF Container --}}
    <div x-show="!isLoading" class="h-full flex items-center justify-center overflow-auto">
        <canvas id="pdf-canvas" class="shadow-2xl"></canvas>
    </div>

    {{-- Navigation Buttons - Positioned at sides --}}
    <div x-show="!isLoading"
        class="absolute inset-y-0 left-0 right-0 flex items-center justify-between pointer-events-none px-4">
        {{-- Previous Button --}}
        <button @click="previousPage()" :disabled="currentPage <= 1" class="btn btn-circle btn-lg pointer-events-auto"
            :class="currentPage <= 1 ? 'btn-disabled' : 'btn-primary'">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        {{-- Next Button --}}
        <button @click="nextPage()" :disabled="currentPage >= totalPages"
            class="btn btn-circle btn-lg pointer-events-auto"
            :class="currentPage >= totalPages ? 'btn-disabled' : 'btn-primary'">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    {{-- Page Counter - Bottom Center --}}
    <div x-show="!isLoading" class="absolute bottom-4 left-1/2 transform -translate-x-1/2">
        <div class="badge badge-lg badge-neutral">
            <span x-text="currentPage"></span> / <span x-text="totalPages"></span>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/5.4.149/pdf.min.mjs" type="module"></script>
<script>
    // Store PDF document outside Alpine's reactive system to avoid proxy issues
    let pdfDocument = null;
    function pdfViewer() {
        return {
            isLoading: false,
            currentPage: 1,
            totalPages: 0,
            searchQuery: '',
            scale: 1.5,

            async init() {
                // Configuramos el worker de PDF.js
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/5.4.149/pdf.worker.min.mjs';
                // Escuchamos el evento de selección de una página desde Livewire
                window.addEventListener('page-selected', (event) => {
                    this.loadPage(event.detail.pageNumber);
                });
                // Obtener query de búsqueda desde la URL para resaltar
                const urlParams = new URLSearchParams(window.location.search);
                this.searchQuery = urlParams.get('q') || '';
                // Load PDF and first page on init
                await this.loadPdf();
                await this.loadPage(1);
            },
            async loadPage(pageNumber) {
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
                const viewport = page.getViewport({ scale: this.scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                // Renderiza la página PDF
                await page.render({
                    canvasContext: context,
                    viewport: viewport
                }).promise;
                // Si tenemos una consulta de búsqueda, intentar resaltarla
                if (this.searchQuery) {
                    await this.highlightSearchTerm(page, viewport);
                }
            },
            async highlightSearchTerm(page, viewport) {
                // Obtener contenido de texto de la página
                const textContent = await page.getTextContent();
                const canvas = document.getElementById('pdf-canvas');
                const context = canvas.getContext('2d');

                // Buscar el término en elementos de texto
                textContent.items.forEach(item => {
                    console.log(item);
                    if (item.str.toLowerCase().includes(this.searchQuery.toLowerCase())) {
                        // Get text position and dimensions
                        const tx = item.transform[4];
                        const ty = item.transform[5];

                        // Calculate width based on text
                        const fontSize = Math.sqrt(item.transform[2] * item.transform[2] + item.transform[3] * item.transform[3]);
                        const textWidth = context.measureText(item.str).width || item.width;

                        // Transform coordinates from PDF space to viewport space
                        const [x, y] = viewport.convertToViewportPoint(tx, ty);
                        const height = fontSize * this.scale;

                        // Dibujar rectángulo de resaltado
                        context.fillStyle = 'rgba(255, 255, 0, 0.4)';
                        context.fillRect(
                            x,
                            y - height,
                            textWidth,
                            height
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
            close() { }
        };
    }
</script>
