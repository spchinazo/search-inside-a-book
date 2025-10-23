<?php

namespace App\Http\Controllers\Api;

use App\Book;
use App\BookPage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Search for text within the book
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $limit = min($request->get('limit', 20), 1000); // Max 1000 results
        $page = max($request->get('page', 1), 1);

        // If query is empty, return all pages
        if (empty(trim($query))) {
            $searchResults = $this->getAllPages($limit, $page);
        } else {
            $searchResults = $this->performSearch($query, $limit, $page);
        }

        return response()->json([
            'success' => true,
            'data' => $searchResults,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $searchResults['total']
            ]
        ]);
    }

    /**
     * Get a specific page by page number
     */
    public function getPageByNumber(Request $request, int $pageNumber): JsonResponse
    {
        $page = BookPage::with('book')->where('page_number', $pageNumber)->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $page->id,
                'page_number' => $page->page_number,
                'text_content' => $page->text_content,
                'book' => [
                    'id' => $page->book->id,
                    'title' => $page->book->title,
                    'author' => $page->book->author
                ]
            ]
        ]);
    }

    /**
     * Get a specific page by ID
     */
    public function getPage(Request $request, int $pageId): JsonResponse
    {
        $page = BookPage::with('book')->find($pageId);

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $page->id,
                'page_number' => $page->page_number,
                'text_content' => $page->text_content,
                'book' => [
                    'id' => $page->book->id,
                    'title' => $page->book->title,
                    'author' => $page->book->author
                ]
            ]
        ]);
    }

    /**
     * Get book information
     */
    public function getBook(): JsonResponse
    {
        $book = Book::with('pages')->first();

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Book not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'description' => $book->description,
                'total_pages' => $book->pages->count()
            ]
        ]);
    }

    /**
     * Get all pages when no search query is provided
     */
    private function getAllPages(int $limit, int $page): array
    {
        $offset = ($page - 1) * $limit;
        
        $results = BookPage::select(['book_pages.*'])
            ->orderBy('page_number', 'asc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        $total = BookPage::count();

        $searchResults = $results->map(function ($page) {
            return [
                'id' => $page->id,
                'page_number' => $page->page_number,
                'snippet' => $this->generateSnippet($page->text_content, '', []),
                'relevance_score' => 0,
                'match_position' => 0
            ];
        });

        return [
            'results' => $searchResults,
            'total' => $total,
            'query' => ''
        ];
    }

    /**
     * Perform the actual search with ranking and snippets
     */
    private function performSearch(string $query, int $limit, int $page): array
    {
        $offset = ($page - 1) * $limit;
        
        // Clean and prepare search terms
        $searchTerms = $this->prepareSearchTerms($query);
        
        // Build the search query with ranking
        $results = BookPage::select([
                'book_pages.*',
                DB::raw("(
                    CASE 
                        WHEN LOWER(text_content) LIKE LOWER('%" . addslashes($query) . "%') THEN 100
                        ELSE (
                            SELECT COUNT(*) 
                            FROM (
                                SELECT unnest(string_to_array(LOWER(text_content), ' ')) as word
                            ) words 
                            WHERE word LIKE LOWER('%" . addslashes($query) . "%')
                        ) * 10
                    END
                ) as relevance_score"),
                DB::raw("position(LOWER('" . addslashes($query) . "') in LOWER(text_content)) as match_position")
            ])
            ->where(function($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $q->orWhere('text_content', 'ILIKE', "%{$term}%");
                }
            })
            ->orderBy('relevance_score', 'desc')
            ->orderBy('match_position', 'asc')
            ->orderBy('page_number', 'asc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        // Get total count for pagination
        $total = BookPage::where(function($q) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                $q->orWhere('text_content', 'ILIKE', "%{$term}%");
            }
        })->count();

        // Generate snippets for each result
        $searchResults = $results->map(function ($page) use ($query, $searchTerms) {
            return [
                'id' => $page->id,
                'page_number' => $page->page_number,
                'snippet' => $this->generateSnippet($page->text_content, $query, $searchTerms),
                'relevance_score' => $page->relevance_score,
                'match_position' => $page->match_position
            ];
        });

        return [
            'results' => $searchResults,
            'total' => $total,
            'query' => $query
        ];
    }

    /**
     * Prepare search terms for better matching
     */
    private function prepareSearchTerms(string $query): array
    {
        // Remove extra whitespace and split into terms
        $terms = array_filter(array_map('trim', explode(' ', $query)));
        
        // Add the full query as a term for exact matches
        $terms[] = $query;
        
        return array_unique($terms);
    }

    /**
     * Generate a snippet with highlighted search terms
     */
    private function generateSnippet(string $text, string $query, array $searchTerms): string
    {
        $snippetLength = 200;
        $text = trim($text);
        
        // Find the best match position
        $bestPosition = 0;
        $bestScore = 0;
        
        foreach ($searchTerms as $term) {
            $position = stripos($text, $term);
            if ($position !== false) {
                $score = strlen($term);
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestPosition = $position;
                }
            }
        }
        
        // Calculate snippet boundaries
        $start = max(0, $bestPosition - $snippetLength / 2);
        $end = min(strlen($text), $start + $snippetLength);
        
        // Adjust start to avoid cutting words
        if ($start > 0) {
            $start = strpos($text, ' ', $start) + 1;
        }
        
        // Adjust end to avoid cutting words
        if ($end < strlen($text)) {
            $end = strrpos(substr($text, 0, $end), ' ');
            if ($end === false) {
                $end = $start + $snippetLength;
            }
        }
        
        $snippet = substr($text, $start, $end - $start);
        
        // Add ellipsis if needed
        if ($start > 0) {
            $snippet = '...' . $snippet;
        }
        if ($end < strlen($text)) {
            $snippet = $snippet . '...';
        }
        
        // Highlight search terms
        foreach ($searchTerms as $term) {
            if (strlen($term) > 2) { // Only highlight terms longer than 2 characters
                $snippet = preg_replace(
                    '/\b(' . preg_quote($term, '/') . ')\b/i',
                    '<mark>$1</mark>',
                    $snippet
                );
            }
        }
        
        return $snippet;
    }
}
