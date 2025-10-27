<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookPage;
use Illuminate\Http\Request;
use App\Http\Resources\SearchResultResource;
use App\Http\Resources\BookResource;

class BookSearchController extends Controller
{
    /**
     * Returns a collection of all available books.
     */
    public function index(Request $request)
    {
        $books = Book::all(); 
        return BookResource::collection($books);
    }

    /**
     * Returns the search results for a specific book, with highlighting enabled.
     */
    public function search(Request $request, Book $book)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return response()->json(['message' => 'The "q" parameter is required for search.'], 400);
        }

        $rawResults = BookPage::search($query, function ($meiliSearch, $query, $options) use ($book) {
            $options['filter'] = 'book_id = ' . $book->id;
            $options['attributesToHighlight'] = ['text_content'];
            $options['highlightPreTag'] = '<em>';
            $options['highlightPostTag'] = '</em>';
            $options['limit'] = 20;
        
            return $meiliSearch->search($query, $options);
        })->raw();

        $hits = $rawResults['hits'] ?? [];

        $results = collect($hits)->map(function ($hit) {
            return new SearchResultResource((object) $hit);
        });

        return $results;
    }

    /**
     * Returns the full content of a specific page.
     */
    public function showPage(BookPage $page)
    {
        return response()->json([
            'page_number' => $page->page_number,
            'content' => $page->text_content,
            'book_title' => $page->book->title,
        ]);
    }
}
