import '../css/app.css';

// PDF.js se cargará desde CDN en el componente visor
// Esto mantiene tu build más pequeño y permite usar la última versión

// Escuchar eventos de Livewire
document.addEventListener('livewire:init', () => {
    Livewire.on('page-selected', (event) => {
        // El componente visor PDF manejará esto
        console.log('Página seleccionada:', event.pageNumber);
    });
});
