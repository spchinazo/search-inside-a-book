<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Book Search</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .search-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .search-header {
            background: #616161ff;
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .search-box {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }
        
        .search-input {
            font-size: 1.1rem;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            transition: border-color 0.3s;
        }
        
        .search-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .search-info {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        
        .results-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        
        .results-list {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .result-item {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .result-item:hover {
            background-color: #f8f9fa;
        }
        
        .result-item.active {
            background-color: #e7f1ff;
            border-left: 4px solid #667eea;
        }
        
        .result-item:last-child {
            border-bottom: none;
        }
        
        .result-page {
            color: #667eea;
            font-weight: bold;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .result-snippet {
            color: #495057;
            line-height: 1.6;
        }
        
        .result-snippet mark {
            background-color: #fff3cd;
            padding: 0.1rem 0.2rem;
            border-radius: 3px;
            font-weight: 600;
        }
        
        .page-viewer {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-height: 70vh;
            overflow-y: auto;
            position: sticky;
            top: 1rem;
        }
        
        .page-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .page-number {
            color: #667eea;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .page-content {
            line-height: 1.8;
            color: #212529;
            white-space: pre-wrap;
            font-family: Georgia, serif;
        }
        
        .page-content mark {
            background-color: #fff3cd;
            padding: 0.1rem 0.2rem;
            border-radius: 3px;
            font-weight: 600;
        }
        
        .loading {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }
        
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.15em;
        }
        
        .no-results {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .no-results-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .badge-count {
            background-color: #667eea;
            font-size: 0.75rem;
        }
        
        .search-time {
            color: #6c757d;
            font-size: 0.85rem;
            margin-left: 1rem;
        }
        
        @media (max-width: 768px) {
            .results-container {
                grid-template-columns: 1fr;
            }
            
            .page-viewer {
                position: static;
            }
        }
    </style>
</head>
<body>
    <div class="search-container">
        <!-- Header -->
        <div class="search-header">
            <h1 class="mb-2">
                Book search
            </h1>
        </div>

        <!-- Search Box -->
        <div class="search-box">
            <div class="input-group">
                <span class="input-group-text bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                    </svg>
                </span>
                <input 
                    type="text" 
                    class="form-control search-input" 
                    id="searchInput" 
                    placeholder="Type the term you want to search for... (e.g., 'the DOM')"
                    autocomplete="off"
                >
            </div>
            <div class="search-info" id="searchInfo">
                Type at least 2 characters to start searching
            </div>
        </div>

        <!-- Results Container -->
        <div class="results-container" id="resultsContainer" style="display: none;">
            <!-- Results List -->
            <div>
                <div class="results-list" id="resultsList">
                    <div class="loading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Viewer -->
            <div>
                <div class="page-viewer" id="pageViewer">
                    <div class="text-center text-muted">
                        <p>Select a result to view the full page</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Search Script -->
    <script>
        // Configuration
        const DEBOUNCE_DELAY = 400;
        const MIN_SEARCH_LENGTH = 2;
        
        // State
        let debounceTimer = null;
        let currentQuery = '';
        let searchResults = [];
        let selectedResultIndex = -1;
        
        // DOM Elements
        const searchInput = document.getElementById('searchInput');
        const searchInfo = document.getElementById('searchInfo');
        const resultsContainer = document.getElementById('resultsContainer');
        const resultsList = document.getElementById('resultsList');
        const pageViewer = document.getElementById('pageViewer');
        
        // Event Listeners
        searchInput.addEventListener('input', handleSearchInput);
        searchInput.addEventListener('keydown', handleKeyNavigation);
        
        // Search input handler with debouncing
        function handleSearchInput(e) {
            const query = e.target.value.trim();
            
            if (debounceTimer) {
                clearTimeout(debounceTimer);
            }
            
            if (query.length < MIN_SEARCH_LENGTH) {
                resetSearch();
                searchInfo.innerHTML = 'Type at least 2 characters to start searching';
                return;
            }
            
            searchInfo.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Searching...';
            
            debounceTimer = setTimeout(() => {
                performSearch(query);
            }, DEBOUNCE_DELAY);
        }
        
        // Perform the search via API
        async function performSearch(query) {
            currentQuery = query;
            
            try {
                const response = await fetch(`/api/book/search?q=${encodeURIComponent(query)}&limit=50`);
                const data = await response.json();
                
                if (data.success) {
                    searchResults = data.results;
                    displayResults(data);
                } else {
                    showError('Erro ao buscar: ' + (data.message || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Search error:', error);
                showError('Erro ao conectar com o servidor');
            }
        }
        
        // Display search results
        function displayResults(data) {
            const { total_results, results, search_time_ms } = data;
            
            if (total_results === 0) {
                showNoResults();
                return;
            }
            
            resultsContainer.style.display = 'grid';
            
            searchInfo.innerHTML = `
                Found <strong>${total_results}</strong> results${total_results !== 1 ? 's' : ''}
                <span class="search-time">(${search_time_ms}ms)</span>
            `;
            
            let html = '';
            results.forEach((result, index) => {
                html += `
                    <div class="result-item" data-index="${index}" data-page="${result.page}">
                        <div class="result-page">
                            Page ${result.page}
                            ${result.match_count_in_page > 1 ? `<span class="badge badge-count">${result.match_count_in_page} occurrences</span>` : ''}
                        </div>
                        <div class="result-snippet">
                            ${result.highlighted_snippet}
                        </div>
                    </div>
                `;
            });
            
            resultsList.innerHTML = html;
            
            // Add click handlers
            document.querySelectorAll('.result-item').forEach(item => {
                item.addEventListener('click', handleResultClick);
            });
            
            // Auto-select first result
            selectResult(0);
        }
        
        // Handle result item click
        function handleResultClick(e) {
            const item = e.currentTarget;
            const index = parseInt(item.dataset.index);
            selectResult(index);
        }
        
        // Select a result and load its page
        function selectResult(index) {
            if (index < 0 || index >= searchResults.length) {
                return;
            }
            
            selectedResultIndex = index;
            const result = searchResults[index];
            
            // Update active state
            document.querySelectorAll('.result-item').forEach((item, i) => {
                item.classList.toggle('active', i === index);
            });
            
            // Scroll to result if needed
            const activeItem = document.querySelector('.result-item.active');
            if (activeItem) {
                activeItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
            
            // Load the page
            loadPage(result.page);
        }
        
        // Load a full page
        async function loadPage(pageNumber) {
            pageViewer.innerHTML = '<div class="loading"><div class="spinner-border text-primary" role="status"></div></div>';
            
            try {
                const response = await fetch(`/api/book/page/${pageNumber}`);
                const data = await response.json();
                
                if (data.success) {
                    displayPage(data.page);
                } else {
                    pageViewer.innerHTML = `<div class="alert alert-danger">Error loading page</div>`;
                }
            } catch (error) {
                console.error('Page load error:', error);
                pageViewer.innerHTML = `<div class="alert alert-danger">Error loading page</div>`;
            }
        }
        
        // Display a full page with highlighted search terms
        function displayPage(page) {
            const highlightedContent = highlightText(page.text_content, currentQuery);
            
            pageViewer.innerHTML = `
                <div class="page-header">
                    <div class="page-number">Page ${page.page}</div>
                </div>
                <div class="page-content">${highlightedContent}</div>
            `;
        }
        
        // Highlight search term in text
        function highlightText(text, query) {
            if (!query) return escapeHtml(text);
            
            const escapedText = escapeHtml(text);
            const escapedQuery = escapeRegex(query);
            const regex = new RegExp(`(${escapedQuery})`, 'gi');
            
            return escapedText.replace(regex, '<mark>$1</mark>');
        }
        
        // Escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Escape regex special characters
        function escapeRegex(text) {
            return text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }
        
        // Handle keyboard navigation
        function handleKeyNavigation(e) {
            if (searchResults.length === 0) return;
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectResult(Math.min(selectedResultIndex + 1, searchResults.length - 1));
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectResult(Math.max(selectedResultIndex - 1, 0));
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (selectedResultIndex >= 0) {
                    // Result already loaded, just scroll to it
                    const activeItem = document.querySelector('.result-item.active');
                    if (activeItem) {
                        activeItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            }
        }
        
        // Show no results message
        function showNoResults() {
            resultsContainer.style.display = 'grid';
            searchInfo.innerHTML = 'No results found. Try different keywords.';
            
            resultsList.innerHTML = `
                <div class="no-results">
                    <h5>No results found</h5>
                    <p>Try using different keywords</p>
                </div>
            `;
            
            pageViewer.innerHTML = `
                <div class="text-center text-muted">
                    <p>No pages to display</p>
                </div>
            `;
        }
        
        // Show error message
        function showError(message) {
            searchInfo.innerHTML = `<span class="text-danger">${message}</span>`;
        }
        
        // Reset search state
        function resetSearch() {
            resultsContainer.style.display = 'none';
            currentQuery = '';
            searchResults = [];
            selectedResultIndex = -1;
        }
        
        // Focus search input on load
        window.addEventListener('load', () => {
            searchInput.focus();
        });
    </script>
</body>
</html>
