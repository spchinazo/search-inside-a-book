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

        // Log for relevance analysis
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

    public function showPage(BookPage $page)
    {
        $cacheKey = "full_page_content_{$page->id}";

        $pageData = Cache::remember($cacheKey, 3600, function () use ($page) {
            $page->load('book');

            return [
                'page_number' => $page->page_number,
                'content' => $page->text_content,
                'book_title' => $page->book->title,
            ];
        });

        return response()->json($pageData);
    }
}