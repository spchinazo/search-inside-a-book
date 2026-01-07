<div class="search-container">
    <header>
        <h1>{{ $book->title }}</h1>
        <p class="subtitle">Search inside the book and discover its contents</p>
    </header>

    <div class="search-box-wrapper">
        <input 
            type="text" 
            wire:model.live.debounce.300ms="query" 
            placeholder="Search for keywords, concepts, or terms..." 
            class="search-input"
        >
        <div wire:loading class="loading-indicator">
            <div class="spinner"></div>
        </div>
    </div>

    @if($selectedPage)
        <div class="page-viewer glass" wire:key="page-viewer">
            <div class="page-header">
                <h2>Page {{ $selectedPage }}</h2>
                <button wire:click="clearSelection" class="btn-close">&times;</button>
            </div>
            <div class="page-content">
                {!! nl2br(e($pageContent)) !!}
            </div>
            <div class="page-footer">
                <button wire:click="clearSelection" class="btn btn-back">Back to search results</button>
            </div>
        </div>
    @else
        <div class="results-container" wire:key="search-results">
            @if(!empty($query))
                <div class="results-stats">
                    Found {{ $total }} matches for "{{ $query }}"
                </div>

                <div class="results-list">
                    @forelse($paginator as $hit)
                        <div class="result-card glass" wire:key="hit-{{ $hit['id'] }}" wire:click="selectPage({{ $hit['page_number'] }})">
                            <div class="result-info">
                                <span class="page-badge">Page {{ $hit['page_number'] }}</span>
                            </div>
                            <div class="result-snippet">
                                {!! $hit['_formatted']['content'] ?? $hit['content'] !!}
                            </div>
                            <div class="result-action">
                                <span class="view-link">View full page &rarr;</span>
                            </div>
                        </div>
                    @empty
                        <div class="no-results">
                            <p>No matches found for your search.</p>
                        </div>
                    @endforelse
                </div>

                <div class="pagination-wrapper">
                    {{ $paginator->links('livewire.custom-pagination') }}
                </div>
            @else
                <div class="search-placeholder">
                    <p>Try searching for "function", "variable", or "loops"</p>
                </div>
            @endif
        </div>
    @endif
</div>
