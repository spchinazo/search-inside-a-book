<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BookSearchService
{
    /**
     * Buscar páginas que contengan el término de búsqueda dado.
     */
    public function search(string $query, int $limit = 50): Collection
    {

        if (empty(trim($query))) {
            return collect();
        }

        // Usamos los scopes definidos en el modelo
        return Page::search($query)
            ->withSearchRank($query)
            ->orderBy('page_number')
            ->limit($limit)
            ->get()
            ->map(fn ($page) => [
                'page_number' => $page->page_number,
                'snippet' => $this->extractSnippet($page->text_content, $query),
                'rank' => $page->search_rank ?? 0,
            ]);
    }

    public function getPage(int $pageNumber): ?Page
    {
        return Page::where('page_number', '=', $pageNumber)->first();
    }

    /**
     * Extraer un snippet alrededor del término de búsqueda con contexto.
     *
     * Esto encuentra la primera ocurrencia del término de búsqueda y extrae contexto circundante para mostrar a los usuarios dónde aparece la coincidencia.
     *
     * @param  string  $text  El texto completo de la página.
     * @param  string  $query  El término de búsqueda.
     * @param  int  $contextLength  Caracteres a mostrar antes y después de la coincidencia.
     * @return string Un snippet con etiquetas <mark> alrededor de las coincidencias.
     */
    private function extractSnippet(string $text, string $query, int $contextLength = 30): string
    {
        // Limpiar el texto, reemplazar múltiples espacios y saltos de línea con un solo espacio
        $cleanText = preg_replace('/\s+/', ' ', trim($text));

        // Encontrar la posición del término de búsqueda (case-insensitive)
        $position = stripos($cleanText, $query);

        // Se hace === false porque stripos puede devolver 0 si la coincidencia está al inicio
        if ($position === false) {
            // Si no se encuentra el término no debería pasar con FTS pero por si acaso
            return Str::limit($cleanText, $contextLength * 2);
        }

        // Calcular los límites del snippet
        $start = max(0, $position - $contextLength); // Inicio del snippet, max entre 0 y la posición menos la longitud del contexto
        $length = strlen($query) + ($contextLength * 2); // Longitud del snippet: longitud del término + contexto antes y después (por eso por 2)
        $snippet = substr($cleanText, $start, $length); // Extraer el snippet

        // Agregar puntos suspensivos si no estamos al inicio o al final del texto en el libro
        $prefix = $start > 0 ? '...' : '';
        $suffix = ($start + $length) < strlen($cleanText) ? '...' : '';

        // Resaltar todas las coincidencias del término de búsqueda (case-insensitive)
        $highlightedSnippet = preg_replace(
            '/('.preg_quote($query, '/').')/i',
            '<mark>$1</mark>',
            $snippet
        );

        return $prefix.$highlightedSnippet.$suffix;
    }

    public function countMatches(string $query): int
    {
        if (empty(trim($query))) {
            return 0;
        }

        return Page::search($query)->count();
    }
}
