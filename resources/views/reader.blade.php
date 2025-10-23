<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eloquent JavaScript - Book Reader</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('build/assets/app-Pgh_gDvZ.css') }}" rel="stylesheet">
</head>
<body>
    <div class="progress-container">
        <div class="progress-bar" id="progressBar"></div>
    </div>

    <div class="reader-container">
        <!-- Header -->
        <div class="reader-header">
            <div class="reader-header-content">
                <div class="book-info">
                    <div class="book-info-cover">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="book-info-details">
                        <h1 id="bookTitle">Eloquent JavaScript</h1>
                        <p id="bookAuthor">by Marijn Haverbeke</p>
                    </div>
                </div>

                <div class="reader-actions">
                    <button class="reader-actions-button" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="fas fa-search"></i>
                        Search
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="reader-main">
            <!-- Page Viewer -->
            <div class="reader-content">
                <div class="page-container">
                    <img id="pageViewer" src="{{ asset('storage/exercise-files/Eloquent_JavaScript_pages/page-001.png') }}" alt="Page 1" class="page-image">
                </div>
            </div>

        </div>

        <!-- Footer Pagination -->
        <div class="reader-footer">
            <div class="footer-pagination">
                <button class="footer-nav-button" id="prevBtn" onclick="previousPage()">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="footer-page-info">
                    <span class="footer-page-display">
                        <span id="currentPage">1</span> / <span id="totalPages">583</span>
                    </span>
                </div>
                <button class="footer-nav-button" id="nextBtn" onclick="nextPage()">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Search Modal -->
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPage = 1;
        let totalPages = 583; // Total number of page images
        let pageViewer = null;

        // Initialize reader
        document.addEventListener('DOMContentLoaded', function() {
            // Get page from URL parameter (support both 'page' and 'location')
            const urlParams = new URLSearchParams(window.location.search);
            const pageParam = urlParams.get('page') || urlParams.get('location');
            
            if (pageParam) {
                currentPage = parseInt(pageParam);
            } else {
                currentPage = 1; // Default to page 1
            }
            
            // Initialize page viewer
            initializePageViewer();
            
            // Update progress bar
            updateProgress();
        });

        // Initialize page viewer
        function initializePageViewer() {
            pageViewer = document.getElementById('pageViewer');
            if (pageViewer) {
                // Set initial page
                goToPage(currentPage);
            }
        }

        // Load specific page image
        function loadPage(pageNumber) {
            if (pageViewer) {
                // Format page number with leading zeros (001, 002, etc.)
                const paddedPageNumber = pageNumber.toString().padStart(3, '0');
                const imageUrl = `{{ asset('storage/exercise-files/Eloquent_JavaScript_pages/page-') }}${paddedPageNumber}.png`;
                
                pageViewer.src = imageUrl;
                pageViewer.alt = `Page ${pageNumber}`;
                
                // Update current page
                currentPage = pageNumber;
                document.getElementById('currentPage').textContent = currentPage;
                
                // Update navigation buttons
                updateNavigation();
                updateProgress();
                
                // Update URL without reload (use 'location' parameter like Aleph Digital)
                const url = new URL(window.location);
                url.searchParams.set('location', currentPage);
                window.history.pushState({}, '', url);
            }
        }

        // Navigation functions
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

        // Update navigation state
        function updateNavigation() {
            document.getElementById('prevBtn').disabled = currentPage <= 1;
            document.getElementById('nextBtn').disabled = currentPage >= totalPages;
        }

        // Update progress bar
        function updateProgress() {
            const progress = (currentPage / totalPages) * 100;
            document.getElementById('progressBar').style.width = `${progress}%`;
        }

        // Modal functions
        function openSearchModal() {
            const searchModal = new bootstrap.Modal(document.getElementById('searchModal'));
            searchModal.show();
            
            // Focus on search input when modal opens
            setTimeout(() => {
                document.getElementById('searchInput').focus();
            }, 500);
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const query = e.target.value.trim();
            if (query.length > 2) {
                performSearch(query);
            } else {
                document.getElementById('searchResults').innerHTML = '';
            }
        });

        // Clear search when modal is closed
        document.getElementById('searchModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('searchInput').value = '';
            document.getElementById('searchResults').innerHTML = '';
        });

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
            // Close the modal
            const searchModal = bootstrap.Modal.getInstance(document.getElementById('searchModal'));
            if (searchModal) {
                searchModal.hide();
            }
        }

        // Go back to search
        function goBack() {
            window.location.href = '/';
        }

        // Show error
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

        // Keyboard navigation
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
            }
        });
    </script>
</body>
</html>