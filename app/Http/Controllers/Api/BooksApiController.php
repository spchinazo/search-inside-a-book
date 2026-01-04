<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\GenericResponse;
use App\Services\BooksService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BooksApiController extends Controller
{
    protected $booksService;

    public function __construct(BooksService $booksService)
    {
        $this->booksService = $booksService;
    }

    public function index(Request $request)
    {
        $term = $request->input('q');
        $perPage = $request->input('per_page', 15);

        $books = $this->booksService->searchBooks($term, $perPage);

        return new GenericResponse([
            'message' => __('app.details.books_found'),
            'status' => 'OK',
            'payload' => $books,
            'code' => Response::HTTP_OK
        ]);
    }

    public function search(Request $request, $bookId)
    {
        $term = $request->input('q');

        if (!$term) {
            return new GenericResponse([
                'message' => __('app.details.search_term_required'),
                'status' => 'ERROR',
                'payload' => null,
                'code' => Response::HTTP_BAD_REQUEST
            ]);
        }
        $bookId = decodeId($bookId);
        $results = $this->booksService->searchInBook($bookId, $term);
        $payload = $this->booksService->formatSearchResponse($results);

        return new GenericResponse([
            'message' => __('app.details.search_results'),
            'status' => 'OK',
            'payload' => $payload,
            'code' => Response::HTTP_OK
        ]);
    }
}
