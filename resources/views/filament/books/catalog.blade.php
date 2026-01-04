<x-filament-panels::page>
    <script>
        let searchTimeout;
        window.searchPagesByText = function(query, callback) {
            if (!query) return;
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const bookId = {{ $this->record->id }};
                const url = "{{ route('books.search', ['book' => ':id']) }}".replace(':id', bookId);
                fetch(`${url}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'OK' && data.payload != null && data.payload.items != null) {
                            callback(data.payload.items);
                        } else {
                            callback([]);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        callback([]);
                    });
            }, 600);
        }
    </script>
    <div 
        id="flipbook-wrapper"
        x-data="{
            init() {
                // Load jQuery if not present
                if (typeof jQuery === 'undefined') {
                    var script = document.createElement('script');
                    script.src = 'https://code.jquery.com/jquery-3.7.1.min.js';
                    script.onload = () => {
                        this.loadFlipbook();
                    };
                    document.head.appendChild(script);
                } else {
                    this.loadFlipbook();
                }
            },
            loadFlipbook() {
               // Load Flipbook JS
                var fbScript = document.createElement('script');
                fbScript.src = '{{ asset('real3d-flipbook-jquery-plugin/js/flipbook.min.js?v=1.0.4') }}';
                fbScript.onload = () => this.initFlipbook();
                document.head.appendChild(fbScript);
            },
            initFlipbook() {
                var container = document.getElementById('flipbook-container');
                var options = {
                    pages: @js($this->getFlipbookPages()),
                    skin: 'dark',
                    startPage: 1,
                    flipSound: true,
                    forceFit: true,
                    responsive: true,
                    viewMode: 'webgl',
                    singlePageMode: false,
                    btnShare: { enabled: false },
                    btnDownloadPdf: { enabled: false },
                    btnDownloadPages: { enabled: false },
                    btnBookmark: { enabled: false },
                    btnPrint: { enabled: false },
                    btnToc: { enabled: false },
                    search: { enabled: true },
                    backgroundPattern: '{{ asset('real3d-flipbook-jquery-plugin/assets/images/woven.png') }}',
                    strings: {
                        print: '{{ __('app.flipbook.print') }}',
                        printLeftPage: '{{ __('app.flipbook.printLeftPage') }}',
                        printRightPage: '{{ __('app.flipbook.printRightPage') }}',
                        printCurrentPage: '{{ __('app.flipbook.printCurrentPage') }}',
                        printAllPages: '{{ __('app.flipbook.printAllPages') }}',

                        download: '{{ __('app.flipbook.download') }}',
                        downloadLeftPage: '{{ __('app.flipbook.downloadLeftPage') }}',
                        downloadRightPage: '{{ __('app.flipbook.downloadRightPage') }}',
                        downloadCurrentPage: '{{ __('app.flipbook.downloadCurrentPage') }}',
                        downloadAllPages: '{{ __('app.flipbook.downloadAllPages') }}',

                        bookmarks: '{{ __('app.flipbook.bookmarks') }}',
                        bookmarkLeftPage: '{{ __('app.flipbook.bookmarkLeftPage') }}',
                        bookmarkRightPage: '{{ __('app.flipbook.bookmarkRightPage') }}',
                        bookmarkCurrentPage: '{{ __('app.flipbook.bookmarkCurrentPage') }}',

                        search: '{{ __('app.flipbook.search') }}',
                        findInDocument: '{{ __('app.flipbook.findInDocument') }}',
                        pagesFoundContaining: '{{ __('app.flipbook.pagesFoundContaining') }}',

                        thumbnails: '{{ __('app.flipbook.thumbnails') }}',
                        tableOfContent: '{{ __('app.flipbook.tableOfContent') }}',
                        share: '{{ __('app.flipbook.share') }}',

                        pressEscToClose: '{{ __('app.flipbook.pressEscToClose') }}',
                    },
                    executeSearchFunction: function(queryText, functionExec) {
                        window.searchPagesByText(queryText, functionExec);
                    }
                };
                window.flipBookEl = new FlipBook(container, options);
            }
        }"
        class="w-full h-[calc(100vh-8rem)] relative"
    >
        <link rel="stylesheet" type="text/css" href="{{ asset('real3d-flipbook-jquery-plugin/css/flipbook.min.css') }}" />
        <div id="flipbook-container" class="w-full h-full"></div>
    </div>
</x-filament-panels::page>
