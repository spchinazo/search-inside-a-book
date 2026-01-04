<?php

namespace App\Http\Controllers;

use App\Services\BooksService;
use Illuminate\Http\Request;

class BooksController extends Controller
{
    protected $booksService;

    public function __construct(BooksService $booksService)
    {
        $this->booksService = $booksService;
    }

    public function index(Request $request)
    {
        $term = $request->input('q');

        $books = $this->booksService->searchBooks($term);

        return response()->json([
            'message' => __('app.details.books_found'),
            'status' => 'OK',
            'payload' => $books,
            'code' => 200
        ]);
    }

    public function search(Request $request, $bookId)
    {
        $term = $request->input('q');

        if (!$term) {
            return response()->json([
                'message' => __('app.details.search_term_required'),
                'status' => 'ERROR',
                'payload' => null,
                'code' => 400
            ], 400);
        }

        $results = $this->booksService->searchInBook($bookId, $term);
        $payload = $this->booksService->formatSearchResponse($results);

        return response()->json([
            'message' => __('app.details.search_results'),
            'status' => 'OK',
            'payload' => $payload,
            'code' => 200
        ]);
    }
}
