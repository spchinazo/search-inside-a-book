<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Input de Búsqueda --}}
    <div class="mb-6">
        <div class="relative">
            <input
                type="text"
                wire:model.live.debounce.500ms="query"
                placeholder="Buscar dentro del libro..."
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                autofocus
            >

            {{-- Spinner de Carga --}}
            <div wire:loading wire:target="query" class="absolute right-3 top-3">
                <svg class="animate-spin h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>

        {{-- Conteo de Resultados --}}
        @if(strlen($query) > 2)
            <p class="text-sm text-gray-600 mt-2">
                Se {{ $this->totalResults === 1 ? 'encontró' : 'encontraron' }} {{ $this->totalResults }} {{ Str::plural('coincidencia', $this->totalResults) }}
            </p>
        @endif
    </div>

    {{-- Resultados de Búsqueda --}}
    @if(strlen($query) > 2)
        <div>
            @forelse($this->results as $result)
                <div
                    wire:click="selectPage({{ $result['page_number'] }})"
                    class="p-3 border-b border-gray-400 hover:bg-neutral-400 cursor-pointer transition-colors {{ $selectedPage === $result['page_number'] ? 'bg-blue-100 border-blue-500' : '' }}"
                >
                    <p class="text-gray-700 leading-relaxed">
                        {!! $result['snippet'] !!}
                    </p>
                    <div class="mt-2">
                        <span class="text-xs text-gray-500">
                            Página {{ $result['page_number'] }} - Relevancia: {{ number_format($result['rank'], 2) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    No se encontraron resultados para "{{ $query }}"
                </div>
            @endforelse
        </div>
    @elseif(strlen($query) > 0)
        <div class="text-center py-8 text-gray-500">
            Por favor ingresa al menos 2 caracteres para buscar
        </div>
    @else
        <div class="text-center py-8 text-gray-500">
            Comienza a escribir para buscar dentro del libro
        </div>
    @endif
</div>
