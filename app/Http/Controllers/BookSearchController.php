<?php

namespace App\Http\Controllers;

use App\Services\BookSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BookSearchController extends Controller
{
    private BookSearchService $searchService;

    public function __construct(BookSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Search for a term in the book
     * 
     * GET /api/book/search?q=term&limit=50
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2|max:200',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid search parameters',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $query = $request->input('q');
            $limit = $request->input('limit', 50);

            $startTime = microtime(true);
            $results = $this->searchService->search($query, $limit);
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'query' => $query,
                'total_results' => count($results),
                'results' => $results,
                'search_time_ms' => $duration,
            ]);
        } catch (\Exception $e) {
            Log::error('Book search error', [
                'query' => $request->input('q'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get a specific page from the book
     * 
     * GET /api/book/page/{pageNumber}
     * 
     * @param int $pageNumber
     * @return JsonResponse
     */
    public function getPage(int $pageNumber): JsonResponse
    {
        try {
            $page = $this->searchService->getPage($pageNumber);

            if (!$page) {
                return response()->json([
                    'success' => false,
                    'message' => 'Page not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'page' => $page,
            ]);
        } catch (\Exception $e) {
            Log::error('Book page retrieval error', [
                'page' => $pageNumber,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the page',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get book statistics
     * 
     * GET /api/book/stats
     * 
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->searchService->getStats();

            return response()->json([
                'success' => true,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            Log::error('Book stats error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving book statistics',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
