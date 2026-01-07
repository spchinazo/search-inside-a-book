<?php

namespace App\Http\Controllers;

use App\Book;
use App\BookPage;
use Illuminate\Http\Request;

class BookController extends Controller
{

    /**
     * Buscar en las páginas de un libro usando Laravel Scout.
     */
    public function search(Request $request, Book $book)
    {
        $query = $request->query('q');

        if (!$query) {
            return response()->json(['error' => 'Query parameter "q" is required'], 400);
        }

        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 10);
        $offset = ($page - 1) * $perPage;

        $results = BookPage::search($query, function ($meilisearch, $query, $options) use ($book, $perPage, $offset) {
            // Filtrar por book_id específico
            $options['filter'] = "book_id = {$book->id}";

            // Paginación
            $options['limit'] = $perPage;
            $options['offset'] = $offset;

            // Configuración de snippets
            $options['attributesToCrop'] = ['content'];
            $options['cropLength'] = 50; // caracteres alrededor
            $options['cropMarker'] = '...';
            
            // Configuración de highlighting
            $options['attributesToHighlight'] = ['content'];
            $options['highlightPreTag'] = '<mark>';
            $options['highlightPostTag'] = '</mark>';

            // Obtener posiciones de las coincidencias
            $options['showMatchesPosition'] = true;
            
            return $meilisearch->search($query, $options);
        })->raw();

        // Procesamos los resultados
        $hits = $results['hits'] ?? [];
        
        $formattedResults = array_map(function ($hit) {
            return [
                'id' => $hit['id'] ?? null,
                'book_id' => $hit['book_id'] ?? null,
                'page_number' => $hit['page_number'] ?? null,
                'snippet' => $hit['_formatted']['content'] ?? ($hit['content'] ?? ''),
                'matches' => $this->formatMatches($hit['_matchesPosition'] ?? []),
            ];
        }, $hits);

        return response()->json([
            'data' => $formattedResults,
            'current_page' => $page,
            'total' => $results['estimatedTotalHits'] ?? ($results['totalHits'] ?? 0),
            'per_page' => $perPage,
        ]);
    }

    /**
     * Formatea las posiciones de las coincidencias para facilitar su uso en el frontend
     */
    private function formatMatches(array $matchesPosition): array
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
}
