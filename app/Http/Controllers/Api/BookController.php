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
     * Buscar en las páginas de un libro usando Laravel Scout.
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
     * Obtener una página específica de un libro.
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
