<div
    class="fixed bg-white w-screen left-0 top-12 p-6 md:p-0 md:absolute md:w-[230px] md:h-auto md:left-auto md:top-auto md:right-0 z-40 rounded-lg bg-theme-bg dark:bg-gray-800 dark:border-gray-800 md:bg-transparent md:dark:bg-transparent md:dark:border-transparent">
    <form>
        <input type="text" wire:model.live.debounce.500ms="query" placeholder="Search inside this book..."
            class="w-full border rounded-lg p-2 mb-4" />

        @if ($query && count($results) === 0)
            <p class="text-gray-500">No matches found.</p>
        @endif

        <div class="space-y-3 bg-white">
            @foreach ($results as $result)
                <a href="{{ route('books.page', ['book' => $book_slug, 'location' => $result['page'], 'term' => $query]) }}"
                    wire:navigate>
                    <div class="border-b pb-2">
                        <div class="text-sm text-gray-500">
                            Page {{ $result['page'] }}
                        </div>
                        <div class="text-base leading-relaxed">
                            {!! $result['snippet'] !!}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </form>
</div>
