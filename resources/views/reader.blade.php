<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eloquent JavaScript - Book Reader</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/sass/app.scss'])
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    </script>
</head>
<body>
    <div class="progress-container">
        <div class="progress-bar" id="progressBar"></div>
    </div>

    <div class="reader-container">
        <x-reader-header />

        <div class="reader-main">
            <div class="reader-content">
                <div class="pdf-container">
                    <div class="pdf-viewer" id="pdfViewer">
                        <div class="pdf-pages-container" id="pageContainer"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="reader-footer">
            <div class="footer-pagination">
                <button class="footer-nav-button" id="prevBtn" onclick="previousPage()">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="footer-page-info">
                    <span class="footer-page-display">
                        <span id="currentPage">1</span> / <span id="totalPages">-</span>
                    </span>
                </div>
                <button class="footer-nav-button" id="nextBtn" onclick="nextPage()">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchModalLabel">
                        <i class="fas fa-search me-2"></i>
                        Search in Book
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="search-container">
                        <input type="text" class="form-control form-control-lg" id="searchInput" placeholder="Search in book..." autocomplete="off">
                        <div class="search-results mt-3" id="searchResults"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="settingsModalLabel">
                        <i class="fas fa-cog me-2"></i>
                        Reader Settings
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="settings-container">
                        <!-- Zoom Settings -->
                        <div class="settings-section">
                            <h6 class="settings-title">
                                <i class="fas fa-search-plus me-2"></i>
                                Zoom
                            </h6>
                            <div class="zoom-controls-settings">
                                <button class="settings-btn" onclick="zoomOut()" title="Zoom Out">
                                    <i class="fas fa-search-minus"></i>
                                </button>
                                <span class="zoom-level-display" id="zoomLevelDisplay">100%</span>
                                <button class="settings-btn" onclick="zoomIn()" title="Zoom In">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                                <button class="settings-btn" onclick="resetZoom()" title="Reset Zoom">
                                    <i class="fas fa-expand-arrows-alt"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Theme Settings -->
                        <div class="settings-section">
                            <h6 class="settings-title">
                                <i class="fas fa-palette me-2"></i>
                                Theme
                            </h6>
                            <div class="theme-controls">
                                <button class="settings-btn theme-toggle" id="settingsThemeToggle" onclick="toggleTheme()">
                                    <i class="fas fa-moon" id="settingsThemeIcon"></i>
                                    <span id="settingsThemeText">Switch to Dark</span>
                                </button>
                            </div>
                        </div>

                        <!-- Display Settings -->
                        <div class="settings-section">
                            <h6 class="settings-title">
                                <i class="fas fa-desktop me-2"></i>
                                Display
                            </h6>
                            <div class="display-controls">
                                <button class="settings-btn" onclick="toggleFullscreen()" title="Toggle Fullscreen">
                                    <i class="fas fa-expand" id="settingsFullscreenIcon"></i>
                                    <span id="settingsFullscreenText">Enter Fullscreen</span>
                                </button>
                            </div>
                        </div>

                        <!-- Keyboard Shortcuts -->
                        <div class="settings-section">
                            <h6 class="settings-title">
                                <i class="fas fa-keyboard me-2"></i>
                                Keyboard Shortcuts
                            </h6>
                            <div class="shortcuts-list">
                                <div class="shortcut-item">
                                    <span class="shortcut-key">← →</span>
                                    <span class="shortcut-desc">Navigate pages</span>
                                </div>
                                <div class="shortcut-item">
                                    <span class="shortcut-key">+ -</span>
                                    <span class="shortcut-desc">Zoom in/out</span>
                                </div>
                                <div class="shortcut-item">
                                    <span class="shortcut-key">F</span>
                                    <span class="shortcut-desc">Fullscreen</span>
                                </div>
                                <div class="shortcut-item">
                                    <span class="shortcut-key">B</span>
                                    <span class="shortcut-desc">Bookmark</span>
                                </div>
                                <div class="shortcut-item">
                                    <span class="shortcut-key">S</span>
                                    <span class="shortcut-desc">Search</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPage = 1;
        let totalPages = 0;
        let pdfDocument = null;
        let currentZoom = 100;
        let bookmarks = JSON.parse(localStorage.getItem('bookmarks') || '[]');
        let isLoading = false;

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const pageParam = urlParams.get('page') || urlParams.get('location');
            
            if (pageParam) {
                const requestedPage = parseInt(pageParam);
                if (isNaN(requestedPage) || requestedPage < 1) {
                    currentPage = 1;
                } else {
                    currentPage = requestedPage;
                }
            } else {
                currentPage = 1;
            }
            
            loadPDF();
        });

        async function loadPDF() {
            try {
                const pdfUrl = "{{ asset('storage/exercise-files/Eloquent_JavaScript.pdf') }}";
                console.log('Loading PDF from:', pdfUrl);
                
                pdfDocument = await pdfjsLib.getDocument(pdfUrl).promise;
                totalPages = pdfDocument.numPages;
                
                console.log('PDF loaded successfully. Total pages:', totalPages);
                
                document.getElementById('totalPages').textContent = totalPages;
                
                // Validate if requested page exists
                if (currentPage > totalPages) {
                    console.warn(`Requested page ${currentPage} exceeds total pages ${totalPages}. Redirecting to page 1.`);
                    currentPage = 1;
                    // Update URL to reflect the correction
                    const url = new URL(window.location);
                    url.searchParams.set('location', currentPage);
                    window.history.replaceState({}, '', url);
                }
                
                initializePageContainer();
                
                goToPage(currentPage);
                
                updateProgress();
            } catch (error) {
                console.error('Error loading PDF:', error);
                showError('Failed to load PDF document: ' + error.message);
            }
        }

        function initializePageContainer() {
            const pageContainer = document.getElementById('pageContainer');
            pageContainer.innerHTML = '';
            
            const pageWrapper = document.createElement('div');
            pageWrapper.className = 'pdf-page-wrapper';
            pageWrapper.id = 'currentPageWrapper';
            
            const placeholder = document.createElement('div');
            placeholder.className = 'pdf-page-placeholder';
            placeholder.innerHTML = `
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Loading page ${currentPage}...</span>
                </div>
            `;
            
            pageWrapper.appendChild(placeholder);
            pageContainer.appendChild(pageWrapper);
        }

        async function renderPage(pageNumber) {
            const pageWrapper = document.getElementById('currentPageWrapper');
            if (!pageWrapper) {
                console.error('Page wrapper not found');
                return;
            }
            
            // Validate page number before rendering
            if (pageNumber < 1 || pageNumber > totalPages) {
                console.error(`Cannot render page ${pageNumber}. Valid range: 1-${totalPages}`);
                pageWrapper.innerHTML = `
                    <div class="error-placeholder">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Page ${pageNumber} does not exist. Valid range: 1-${totalPages}</span>
                    </div>
                `;
                return;
            }
            
            try {
                console.log(`Rendering PDF page ${pageNumber}`);
                const page = await pdfDocument.getPage(pageNumber);
                const viewport = page.getViewport({ scale: currentZoom / 100 });
                
                console.log(`Page ${pageNumber} viewport:`, viewport);
                
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                canvas.className = 'pdf-page-canvas';
                
                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };
                
                await page.render(renderContext).promise;
                
                console.log(`Page ${pageNumber} rendered successfully`);
                
                pageWrapper.innerHTML = '';
                pageWrapper.appendChild(canvas);
                
            } catch (error) {
                console.error(`Error rendering page ${pageNumber}:`, error);
                pageWrapper.innerHTML = `
                    <div class="error-placeholder">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Error loading page ${pageNumber}: ${error.message}</span>
                    </div>
                `;
            }
        }

        async function loadPage(pageNumber) {
            // Prevent multiple simultaneous loads
            if (isLoading) {
                console.log('Page load already in progress, skipping...');
                return;
            }
            
            // Validate page number
            if (pageNumber < 1 || pageNumber > totalPages) {
                console.error(`Invalid page number: ${pageNumber}. Valid range: 1-${totalPages}`);
                showError(`Page ${pageNumber} does not exist. Please choose a page between 1 and ${totalPages}.`);
                return;
            }
            
            isLoading = true;
            currentPage = pageNumber;
            document.getElementById('currentPage').textContent = currentPage;
            
            const pageWrapper = document.getElementById('currentPageWrapper');
            if (pageWrapper) {
                pageWrapper.innerHTML = `
                    <div class="pdf-page-placeholder">
                        <div class="loading-spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>Loading page ${pageNumber}...</span>
                        </div>
                    </div>
                `;
            }
            
            try {
                await renderPage(pageNumber);
                
                updateNavigation();
                updateProgress();
                updateBookmarkButton();
                
                const url = new URL(window.location);
                url.searchParams.set('location', currentPage);
                window.history.pushState({}, '', url);
            } catch (error) {
                console.error(`Error loading page ${pageNumber}:`, error);
                showError(`Failed to load page ${pageNumber}: ${error.message}`);
            } finally {
                isLoading = false;
            }
        }

        function previousPage() {
            if (currentPage > 1) {
                loadPage(currentPage - 1);
            }
        }

        function nextPage() {
            if (currentPage < totalPages) {
                loadPage(currentPage + 1);
            }
        }

        function goToPage(pageNumber) {
            const page = parseInt(pageNumber);
            if (page >= 1 && page <= totalPages) {
                loadPage(page);
            }
        }

        function updateNavigation() {
            document.getElementById('prevBtn').disabled = currentPage <= 1;
            document.getElementById('nextBtn').disabled = currentPage >= totalPages;
        }

        function updateProgress() {
            const progress = (currentPage / totalPages) * 100;
            document.getElementById('progressBar').style.width = `${progress}%`;
        }

        function openSearchModal() {
            const searchModal = new bootstrap.Modal(document.getElementById('searchModal'));
            searchModal.show();
            
            setTimeout(() => {
                document.getElementById('searchInput').focus();
            }, 500);
        }

        document.getElementById('searchInput').addEventListener('input', function(e) {
            const query = e.target.value.trim();
            if (query.length > 2) {
                performSearch(query);
            } else {
                document.getElementById('searchResults').innerHTML = '';
            }
        });

        document.getElementById('searchModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('searchInput').value = '';
            document.getElementById('searchResults').innerHTML = '';
        });

        function toggleTheme() {
            const body = document.body;
            const themeIcon = document.getElementById('themeIcon');
            const themeText = document.getElementById('themeText');
            const settingsThemeIcon = document.getElementById('settingsThemeIcon');
            const settingsThemeText = document.getElementById('settingsThemeText');
            
            if (body.classList.contains('dark-theme')) {
                body.classList.remove('dark-theme');
                if (themeIcon) themeIcon.className = 'fas fa-moon';
                if (themeText) themeText.textContent = 'Dark';
                if (settingsThemeIcon) settingsThemeIcon.className = 'fas fa-moon';
                if (settingsThemeText) settingsThemeText.textContent = 'Switch to Dark';
                localStorage.setItem('theme', 'light');
            } else {
                body.classList.add('dark-theme');
                if (themeIcon) themeIcon.className = 'fas fa-sun';
                if (themeText) themeText.textContent = 'Light';
                if (settingsThemeIcon) settingsThemeIcon.className = 'fas fa-sun';
                if (settingsThemeText) settingsThemeText.textContent = 'Switch to Light';
                localStorage.setItem('theme', 'dark');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-theme');
                document.getElementById('themeIcon').className = 'fas fa-sun';
                document.getElementById('themeText').textContent = 'Light';
            }
            
            updateBookmarkButton();
        });

        function zoomIn() {
            currentZoom = Math.min(currentZoom + 25, 300);
            updateZoom();
        }

        function zoomOut() {
            currentZoom = Math.max(currentZoom - 25, 50);
            updateZoom();
        }

        function resetZoom() {
            currentZoom = 100;
            updateZoom();
        }

        function updateZoom() {
            const pageContainer = document.getElementById('pageContainer');
            if (pageContainer) {
                pageContainer.style.transform = `scale(${currentZoom / 100})`;
                pageContainer.style.transformOrigin = 'center center';
                
                const zoomLevel = document.getElementById('zoomLevel');
                const zoomLevelDisplay = document.getElementById('zoomLevelDisplay');
                if (zoomLevel) zoomLevel.textContent = `${currentZoom}%`;
                if (zoomLevelDisplay) zoomLevelDisplay.textContent = `${currentZoom}%`;
            }
        }

        function toggleFullscreen() {
            const fullscreenIcon = document.getElementById('fullscreenIcon');
            const settingsFullscreenIcon = document.getElementById('settingsFullscreenIcon');
            const settingsFullscreenText = document.getElementById('settingsFullscreenText');
            
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().then(() => {
                    if (fullscreenIcon) fullscreenIcon.className = 'fas fa-compress';
                    if (settingsFullscreenIcon) settingsFullscreenIcon.className = 'fas fa-compress';
                    if (settingsFullscreenText) settingsFullscreenText.textContent = 'Exit Fullscreen';
                });
            } else {
                document.exitFullscreen().then(() => {
                    if (fullscreenIcon) fullscreenIcon.className = 'fas fa-expand';
                    if (settingsFullscreenIcon) settingsFullscreenIcon.className = 'fas fa-expand';
                    if (settingsFullscreenText) settingsFullscreenText.textContent = 'Enter Fullscreen';
                });
            }
        }

        function toggleBookmark() {
            const pageNumber = currentPage;
            const bookmarkIndex = bookmarks.indexOf(pageNumber);
            
            if (bookmarkIndex > -1) {
                bookmarks.splice(bookmarkIndex, 1);
            } else {
                bookmarks.push(pageNumber);
            }
            
            localStorage.setItem('bookmarks', JSON.stringify(bookmarks));
            updateBookmarkButton();
        }

        function updateBookmarkButton() {
            const isBookmarked = bookmarks.includes(currentPage);
            const bookmarkIcon = document.getElementById('bookmarkIcon');
            const bookmarkText = document.getElementById('bookmarkText');
            
            if (isBookmarked) {
                bookmarkIcon.className = 'fas fa-bookmark';
                bookmarkText.textContent = 'Bookmarked';
            } else {
                bookmarkIcon.className = 'far fa-bookmark';
                bookmarkText.textContent = 'Bookmark';
            }
        }

        async function performSearch(query) {
            try {
                const response = await fetch(`/api/search?q=${encodeURIComponent(query)}&limit=10`);
                const data = await response.json();
                
                if (data.success) {
                    displaySearchResults(data.data.results);
                } else {
                    console.error('Search failed:', data.message);
                    document.getElementById('searchResults').innerHTML = '<div class="text-center text-muted py-3">Search failed</div>';
                }
            } catch (error) {
                console.error('Search error:', error);
                document.getElementById('searchResults').innerHTML = '<div class="text-center text-muted py-3">Search error</div>';
            }
        }

        function displaySearchResults(results) {
            const container = document.getElementById('searchResults');
            
            if (results.length === 0) {
                container.innerHTML = '<div class="text-center text-muted py-3">No results found</div>';
                return;
            }

            let html = '';
            results.forEach(result => {
                html += `
                    <div class="search-result" onclick="goToPageAndCloseModal(${result.page_number})">
                        <div class="fw-semibold">Page ${result.page_number}</div>
                        <div class="search-result-snippet">${result.snippet}</div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        function goToPageAndCloseModal(pageNumber) {
            goToPage(pageNumber);
            const searchModal = bootstrap.Modal.getInstance(document.getElementById('searchModal'));
            if (searchModal) {
                searchModal.hide();
            }
        }

        function goBack() {
            window.location.href = '/';
        }

        function showError(message) {
            document.getElementById('pageContent').innerHTML = `
                <div class="text-center text-danger py-5">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h4>Error</h4>
                    <p>${message}</p>
                    <button class="btn btn-primary" onclick="goBack()">Back to Search</button>
                </div>
            `;
        }

        document.addEventListener('keydown', function(e) {
            if (e.target.tagName === 'INPUT') return;
            
            switch(e.key) {
                case 'ArrowLeft':
                    e.preventDefault();
                    previousPage();
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    nextPage();
                    break;
                case 'Home':
                    e.preventDefault();
                    goToPage(1);
                    break;
                case 'End':
                    e.preventDefault();
                    goToPage(totalPages);
                    break;
                case '+':
                case '=':
                    e.preventDefault();
                    zoomIn();
                    break;
                case '-':
                    e.preventDefault();
                    zoomOut();
                    break;
                case '0':
                    e.preventDefault();
                    resetZoom();
                    break;
                case 'f':
                case 'F':
                    e.preventDefault();
                    toggleFullscreen();
                    break;
                case 'b':
                case 'B':
                    e.preventDefault();
                    toggleBookmark();
                    break;
                case 's':
                case 'S':
                    e.preventDefault();
                    document.getElementById('searchModal').click();
                    break;
            }
        });
    </script>
</body>
</html>