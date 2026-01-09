<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookSearchController extends Controller
{
    public function __construct(private BookSearchService $searchService) {}

    /**
     * Endpoint para obtener page
     * GET /api/pages/{pageNumber}
     */
    public function getPage(int $pageNumber): JsonResponse {
        $page = $this->searchService->getPage($pageNumber);

        if(!$page){
            return response()->json([
                'error' => 'Página no encontrada'
            ],404);
        }

        return response()->json([
            'page_number' => $page->page_number,
            'text_content' => $page->text_content,
            'pdf_urf' => asset('storage/exercise-files/Eloquent_Javascript.pdf'),
        ]);

    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $limit = min($request->input('limit', 50), 100); // Máximo 100 resultados

        if (strlen(trim($query)) < 2) {
            return response()->json([
                'results' => [],
                'total' => 0,
                'message' => 'El término de búsqueda debe tener al menos 2 caracteres.',
            ]);
        }

        $results = $this->searchService->search($query, $limit);
        $total = $this->searchService->countMatches($query);

        return response()->json([
            'results' => $results,
            'total' => $total,
            'query' => $query,
        ], options: JSON_INVALID_UTF8_SUBSTITUTE);
    }
}
