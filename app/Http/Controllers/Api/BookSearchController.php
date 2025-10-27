<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookPage;
use App\Http\Resources\SearchResultResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class BookSearchController extends Controller
{
    /**
     * Returns the search results for a specific book.
     */
    public function search(Request $request, Book $book)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return response()->json(['message' => 'The "q" parameter is required for search.'], 400);
        }

        $results = BookPage::search($query)
            ->where('book_id', $book->id)
            ->take(20)
            ->get(); 
        
        return SearchResultResource::collection($results);
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