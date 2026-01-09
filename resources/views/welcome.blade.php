<x-layouts.app>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mx-auto px-4">
        {{-- Lado izquierdo: Interfaz de Búsqueda --}}
        <div>
            <livewire:book-search />
        </div>

        {{-- Lado derecho: Visor PDF --}}
        <div class="lg:sticky lg:top-6 lg:h-screen">
            <x-pdf-viewer />
        </div>
    </div>
</x-layouts.app>
