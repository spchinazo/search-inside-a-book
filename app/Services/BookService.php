<?php

namespace App\Services;

use App\Book;
use App\BookPage;
use Illuminate\Database\Eloquent\Collection;

class BookService
{
    /**
     * Buscar en las páginas de un libro usando Laravel Scout (Meilisearch).
     */
    public function search(Book $book, string $query, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;

        $results = BookPage::search($query, function ($meilisearch, $query, $options) use ($book, $perPage, $offset) {
            // Filtrar por book_id específico
            $options['filter'] = "book_id = {$book->id}";

            // Paginación
            $options['limit'] = $perPage;
            $options['offset'] = $offset;

            // Configuración de snippets
            $options['attributesToCrop'] = ['content'];
            $options['cropLength'] = 50;
            $options['cropMarker'] = '...';
            
            // Configuración de highlighting
            $options['attributesToHighlight'] = ['content'];
            $options['highlightPreTag'] = '<mark>';
            $options['highlightPostTag'] = '</mark>';

            // Obtener posiciones de las coincidencias
            $options['showMatchesPosition'] = true;
            
            return $meilisearch->search($query, $options);
        })->raw();

        return [
            'hits' => $results['hits'] ?? [],
            'total' => $results['estimatedTotalHits'] ?? ($results['totalHits'] ?? 0),
            'page' => $page,
            'per_page' => $perPage,
        ];
    }

    /**
     * Formatea las posiciones de las coincidencias.
     */
    public function formatMatches(array $matchesPosition): array
    {
        $matches = [];
        
        foreach ($matchesPosition as $field => $positions) {
            foreach ($positions as $position) {
                $matches[] = [
                    'start' => $position['start'],
                    'length' => $position['length'],
                ];
            }
        }
        
        return $matches;
    }

    /**
     * Obtener una página específica de un libro.
     */
    public function getPage(Book $book, int $pageNumber)
    {
        return $book->pages()->where('page_number', $pageNumber)->first();
    }
}
