<div class="container mx-auto p-4">
    <div class="mb-4">
        <div class="relative">
            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                <!-- Search icon -->
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M10.5 3a7.5 7.5 0 105.3 12.9l3.9 3.9a1.25 1.25 0 001.77-1.76l-3.9-3.9A7.5 7.5 0 0010.5 3zm0 2.5a5 5 0 100 10 5 5 0 000-10z" clip-rule="evenodd" />
                </svg>
            </span>
            <input
                type="text"
                class="w-full rounded-xl border border-gray-300 bg-white pl-10 pr-24 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Buscar en el libro (mín. 2 letras)"
                wire:model.live.debounce.300ms="query"
                autofocus
            />
            @if(!empty($query))
                <button
                    type="button"
                    wire:click="$set('query','')"
                    class="absolute inset-y-0 right-3 my-auto h-9 px-3 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition"
                    aria-label="Limpiar"
                >Limpiar</button>
            @endif
        </div>
        <p class="mt-2 text-sm text-gray-500">Consejo: prueba términos como "JavaScript", "Project" o "Layout".</p>
    </div>

    <div class="max-h-[70vh] overflow-y-auto">
        <div wire:loading class="text-sm text-gray-500 mb-2">buscando resultados...</div>

        @if(!empty($query) && count($results) === 0)
            <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-gray-700">No se encontraron resultados.</div>
        @endif

        @if(count($results) > 0)
            <div class="mb-2 text-sm text-gray-500">{{ count($results) }} resultado(s)</div>
            <ol class="space-y-3">
                @foreach($results as $result)
                    <li>
                        <a href="{{ route('book.page', ['page' => $result['page']]) }}" class="block rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:shadow-md hover:border-indigo-300 transition">
                            <div class="flex justify-between items-start">
                                <div class="mr-2">
                                    <div class="font-semibold text-gray-800">Página {{ $result['page'] }}</div>
                                    <div class="text-gray-600 leading-relaxed">
                                        {!! $result['snippet'] !!}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>
</div>
