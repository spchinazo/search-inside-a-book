<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SearchController extends Controller
{
    private $bookData = null;

    /**
     * Load book data from JSON file
     */
    private function loadBookData(): array
    {
        if ($this->bookData === null) {
            $jsonPath = storage_path('exercise-files/Eloquent_JavaScript.json');
            
            if (!file_exists($jsonPath)) {
                throw new \Exception('Book data file not found');
            }
            
            $this->bookData = json_decode(file_get_contents($jsonPath), true);
            
            if (!$this->bookData) {
                throw new \Exception('Failed to parse book data');
            }
        }
        
        return $this->bookData;
    }

    /**
     * Search for text within the book
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $limit = min($request->get('limit', 20), 1000); // Max 1000 results
        $page = max($request->get('page', 1), 1);

        try {
            $bookData = $this->loadBookData();
            
            // If query is empty, return all pages
            if (empty(trim($query))) {
                $searchResults = $this->getAllPages($bookData, $limit, $page);
            } else {
                $searchResults = $this->performSearch($bookData, $query, $limit, $page);
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Get a specific page by page number
     */
    public function getPageByNumber(Request $request, int $pageNumber): JsonResponse
    {
        try {
            $bookData = $this->loadBookData();
            
            $page = collect($bookData)->firstWhere('page', $pageNumber);
            
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
                    'id' => $pageNumber,
                    'page_number' => $page['page'],
                    'text_content' => $page['text_content'],
                    'book' => [
                        'id' => 1,
                        'title' => 'Eloquent JavaScript',
                        'author' => 'Marijn Haverbeke'
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load page: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Get a specific page by ID (same as page number for JSON implementation)
     */
    public function getPage(Request $request, int $pageId): JsonResponse
    {
        return $this->getPageByNumber($request, $pageId);
    }

    /**
     * Get book information
     */
    public function getBook(): JsonResponse
    {
        try {
            $bookData = $this->loadBookData();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => 1,
                    'title' => 'Eloquent JavaScript',
                    'author' => 'Marijn Haverbeke',
                    'description' => 'A Modern Introduction to Programming - 3rd Edition',
                    'total_pages' => count($bookData)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load book: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Get all pages when no search query is provided
     */
    private function getAllPages(array $bookData, int $limit, int $page): array
    {
        $offset = ($page - 1) * $limit;
        $total = count($bookData);
        
        $results = array_slice($bookData, $offset, $limit);
        
        $searchResults = array_map(function ($pageData, $index) {
            return [
                'id' => $pageData['page'],
                'page_number' => $pageData['page'],
                'snippet' => $this->generateSnippet($pageData['text_content'], '', []),
                'relevance_score' => 0,
                'match_position' => 0
            ];
        }, $results, array_keys($results));

        return [
            'results' => $searchResults,
            'total' => $total,
            'query' => ''
        ];
    }

    /**
     * Perform the actual search with ranking and snippets
     */
    private function performSearch(array $bookData, string $query, int $limit, int $page): array
    {
        $offset = ($page - 1) * $limit;
        
        // Clean and prepare search terms
        $searchTerms = $this->prepareSearchTerms($query);
        
        // Search through all pages
        $results = [];
        foreach ($bookData as $index => $pageData) {
            $text = $pageData['text_content'];
            $relevanceScore = $this->calculateRelevanceScore($text, $query, $searchTerms);
            $matchPosition = stripos($text, $query);
            
            if ($relevanceScore > 0) {
                $results[] = [
                    'index' => $index,
                    'page_data' => $pageData,
                    'relevance_score' => $relevanceScore,
                    'match_position' => $matchPosition !== false ? $matchPosition : 0
                ];
            }
        }
        
        // Sort by relevance score (descending) then by match position (ascending)
        usort($results, function($a, $b) {
            if ($a['relevance_score'] == $b['relevance_score']) {
                return $a['match_position'] - $b['match_position'];
            }
            return $b['relevance_score'] - $a['relevance_score'];
        });
        
        // Get total count for pagination
        $total = count($results);
        
        // Apply pagination
        $paginatedResults = array_slice($results, $offset, $limit);
        
        // Generate snippets for each result
        $searchResults = array_map(function ($result) use ($query, $searchTerms) {
            return [
                'id' => $result['page_data']['page'],
                'page_number' => $result['page_data']['page'],
                'snippet' => $this->generateSnippet($result['page_data']['text_content'], $query, $searchTerms),
                'relevance_score' => $result['relevance_score'],
                'match_position' => $result['match_position']
            ];
        }, $paginatedResults);

        return [
            'results' => $searchResults,
            'total' => $total,
            'query' => $query
        ];
    }

    /**
     * Calculate relevance score for a text
     */
    private function calculateRelevanceScore(string $text, string $query, array $searchTerms): int
    {
        $score = 0;
        $textLower = strtolower($text);
        $queryLower = strtolower($query);
        
        // Exact phrase match gets highest score
        if (strpos($textLower, $queryLower) !== false) {
            $score += 100;
        }
        
        // Word frequency scoring
        foreach ($searchTerms as $term) {
            $termLower = strtolower($term);
            if (strlen($term) > 2) { // Only count terms longer than 2 characters
                $count = substr_count($textLower, $termLower);
                $score += $count * 10;
            }
        }
        
        return $score;
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