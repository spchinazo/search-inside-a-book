<?php

namespace App\Http\Controllers\Api;

use App\Book;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchBookRequest;
use App\Http\Resources\BookPageResource;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookController extends Controller
{
    protected $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * Buscar en las páginas de un libro.
     * 
     * Permite realizar búsquedas de texto completo dentro del contenido de las páginas de un libro específico.
     * Los resultados incluyen fragmentos (snippets) donde se encontró el término buscado con resaltado.
     * 
     * @group Books
     * @urlParam book int required ID del libro. Example: 1
     * @queryParam q string required El término de búsqueda. Example: DOM
     * @queryParam page int Página de resultados. Example: 1
     * @queryParam per_page int Cantidad de resultados por página (max 100). Example: 10
     * 
     * @response {
     *  "data": [
     *    {
     *      "book_id": 1,
     *      "page_number": 27,
     *      "content": null,
     *      "snippet": "...it. The same goes for the exercises. Don’t assume you understand them until you’ve actually written a working solution. I recommend you try your solutions to exercises in an actual <mark>JavaScript</mark> interpreter. That way, you’ll get immediate feedback on whether what you are doing is working, and...",
     *      "matches": [
     *         {"start": 0, "length": 10},
     *         {"start": 61, "length": 10}
     *      ]
     *    }
     *  ],
     *  "current_page": 1,
     *  "total": 1,
     *  "per_page": 10
     * }
     */
    public function search(SearchBookRequest $request, Book $book): JsonResponse
    {
        $validated = $request->validated();
        
        $results = $this->bookService->search(
            $book,
            $validated['q'],
            (int) ($validated['page'] ?? 1),
            (int) ($validated['per_page'] ?? 10)
        );

        $formattedHits = array_map(function ($hit) {
            $hit['matches'] = $this->bookService->formatMatches($hit['_matchesPosition'] ?? []);
            $hit['snippet'] = $hit['_formatted']['content'] ?? ($hit['content'] ?? '');
            return $hit;
        }, $results['hits']);

        return response()->json([
            'data' => BookPageResource::collection($formattedHits),
            'current_page' => $results['page'],
            'total' => $results['total'],
            'per_page' => $results['per_page'],
        ]);
    }

    /**
     * Obtener una página específica.
     * 
     * Retorna el contenido completo y metadatos de una página específica de un libro.
     * 
     * @group Books
     * @urlParam book int required ID del libro. Example: 1
     * @urlParam pageNumber int required El número de la página a obtener. Example: 42
     * 
     * @response {
     *  "book_id": 1,
     *  "page_number": 42,
     *  "content": "El contenido completo de la página...",
     *  "snippet": null,
     *  "matches": []
     * }
     * @response 404 {
     *  "error": "Page not found",
     *  "message": "La página 42 no existe para este libro."
     * }
     */
    public function getPage(Book $book, int $pageNumber): JsonResponse
    {
        $page = $this->bookService->getPage($book, $pageNumber);

        if (!$page) {
            return response()->json([
                'error' => 'Page not found',
                'message' => "La página {$pageNumber} no existe para este libro."
            ], 404);
        }

        return (new BookPageResource($page))
            ->response()
            ->setStatusCode(200);
    }
}
