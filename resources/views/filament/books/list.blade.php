<x-filament-panels::page>
    <div class="mb-8">
        <div class="relative max-w-md mx-auto">
            <x-heroicon-m-magnifying-glass
                class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="{{ __('app.details.search_books') }}"
                class="w-full pl-12 pr-4 py-3 bg-slate-900/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 transition-all duration-200">
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6">
        @forelse($this->getTableQueryProperty() as $book)
            <a href="{{ \App\Filament\Resources\Books\BookResource::getUrl('view', ['record' => $book->id ]) }}"
                class="group relative bg-gradient-to-br from-slate-900/80 to-slate-800/50 backdrop-blur-sm rounded-3xl p-6 border border-slate-700/50 hover:border-emerald-500/50 hover:shadow-2xl hover:shadow-emerald-500/25 hover:-translate-y-2 transition-all duration-500">
                {{-- Portada --}}
                <div class="relative mb-6 h-64 rounded-2xl overflow-hidden shadow-2xl mx-auto">
                    <img src="{{ Storage::disk($book->disk)->url($book->front) }}" alt="{{ $book->title }}"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute top-4 left-4 right-4 flex gap-2">
                        <span
                            class="bg-slate-900/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-semibold text-emerald-400 border border-emerald-500/50">
                            {{ strtoupper($book->lang) }}
                        </span>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-white mb-3 leading-tight line-clamp-2 group-hover:text-emerald-400 transition-colors">
                    {{ $book->title }}
                </h3>
                <p class="text-sm">ISBN {{ $book->isbn }}</p>
            </a>
        @empty
            <div class="col-span-full text-center py-20">
                <x-heroicon-o-book-open class="w-24 h-24 text-slate-500 mx-auto mb-4" />
                <h3 class="text-2xl font-bold text-white mb-2">{{ __('app.details.empty') }}</h3>
                <p class="text-slate-400">{{ __('app.details.description') }}.</p>
            </div>
        @endforelse
    </div>

    @if ($this->getTableQueryProperty()->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $this->getTableQueryProperty()->links() }}
        </div>
    @endif
</x-filament-panels::page>

