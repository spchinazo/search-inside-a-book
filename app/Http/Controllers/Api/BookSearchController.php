<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookPage;
use App\Http\Requests\SearchBookRequest;
use App\Http\Resources\SearchResultResource;
use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BookSearchController extends Controller
{
    public function index()
    {
        $books = Cache::remember('all_books_list', 3600, function () {
            return Book::all();
        });

        return BookResource::collection($books);
    }

    public function search(SearchBookRequest $request, Book $book)
    {
        $validated = $request->validated();
        $query = $validated['q'];
        $page = $validated['page'] ?? 1;
        $perPage = 20;

        Log::channel('searches')->info('Search performed', [
            'query' => $query,
            'book_id' => $book->id,
            'ip' => $request->ip(),
            'timestamp' => now(),
        ]);

        $rawResults = BookPage::search($query, function ($meiliSearch, $query, $options) use ($book, $page, $perPage) {
            $options['filter'] = 'book_id = ' . $book->id;
            $options['attributesToHighlight'] = ['text_content'];
            $options['highlightPreTag'] = '<em>';
            $options['highlightPostTag'] = '</em>';
            $options['cropLength'] = 200;
            $options['offset'] = ($page - 1) * $perPage;
            $options['limit'] = $perPage;
        
            return $meiliSearch->search($query, $options);
        })->raw();

        $hits = $rawResults['hits'] ?? [];
        $totalHits = $rawResults['estimatedTotalHits'] ?? 0;

        $results = collect($hits)->map(function ($hit) {
            return new SearchResultResource($hit);
        });

        return response()->json([
            'data' => $results,
            'meta' => [
                'total' => $totalHits,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($totalHits / $perPage),
            ]
        ], 200);
    }

    /**
     * Returns the full page content for a given book and page number.
     * 
     * @param Book $book - The book (route model binding)
     * @param int $pageNumber - The page number (1, 2, 3, etc.)
     */
    public function showPage(Book $book, int $pageNumber)
    {
        $bookPage = BookPage::where('book_id', $book->id)
            ->where('page_number', $pageNumber)
            ->firstOrFail();

        $cacheKey = "full_page_content_{$book->id}_{$pageNumber}";

        $pageData = Cache::remember($cacheKey, 3600, function () use ($bookPage, $book) {
            return [
                'book_id' => $book->id,
                'book_title' => $book->title,
                'page_number' => $bookPage->page_number,
                'content' => $bookPage->text_content,
            ];
        });

        return response()->json($pageData);
    }
}