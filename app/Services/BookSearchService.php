<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class BookSearchService
{
    private const CACHE_KEY = 'book_content';
    private const CACHE_TTL = 3600; // 1 hour
    private const CONTEXT_CHARS = 120; // Characters to show before/after match
    
    private array $bookContent;

    public function __construct()
    {
        $this->loadBookContent();
    }

    /**
     * Load book content from JSON file with caching
     */
    private function loadBookContent(): void
    {
        $this->bookContent = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $path = storage_path('exercise-files/Eloquent_JavaScript.json');
            
            if (!file_exists($path)) {
                throw new \RuntimeException("Book file not found at: {$path}");
            }

            $content = file_get_contents($path);
            return json_decode($content, true);
        });
    }

    /**
     * Search for a term in the book content
     * 
     * @param string $query Search term
     * @param int $limit Maximum number of results to return
     * @return array Array of search results with page, snippet, and position info
     */
    public function search(string $query, int $limit = 50): array
    {
        if (empty(trim($query))) {
            return [];
        }

        $results = [];
        $query = trim($query);
        
        foreach ($this->bookContent as $page) {
            $pageNumber = $page['page'];
            $content = $page['text_content'];
            
            // Case-insensitive search for all matches in the page
            $matches = $this->findMatches($content, $query);
            
            foreach ($matches as $match) {
                $results[] = [
                    'page' => $pageNumber,
                    'snippet' => $match['snippet'],
                    'position' => $match['position'],
                    'highlighted_snippet' => $match['highlighted_snippet'],
                    'match_count_in_page' => count($matches),
                ];
                
                if (count($results) >= $limit) {
                    break 2; // Break both loops
                }
            }
        }

        return $results;
    }

    /**
     * Find all matches of the query in the text
     * 
     * @param string $text Text to search in
     * @param string $query Search term
     * @return array Array of matches with snippet and position
     */
    private function findMatches(string $text, string $query): array
    {
        $matches = [];
        $offset = 0;
        $queryLength = mb_strlen($query);
        
        while (($pos = mb_stripos($text, $query, $offset)) !== false) {
            $start = max(0, $pos - self::CONTEXT_CHARS);
            $end = min(mb_strlen($text), $pos + $queryLength + self::CONTEXT_CHARS);
            $snippetLength = $end - $start;
            
            $snippet = mb_substr($text, $start, $snippetLength);
            
            $prefix = $start > 0 ? '...' : '';
            $suffix = $end < mb_strlen($text) ? '...' : '';
            $snippet = $prefix . $snippet . $suffix;
            
            $highlightedSnippet = $this->highlightQuery($snippet, $query);
            
            $matches[] = [
                'position' => $pos,
                'snippet' => $snippet,
                'highlighted_snippet' => $highlightedSnippet,
            ];
            
            $offset = $pos + $queryLength;
        }
        
        return $matches;
    }

    /**
     * Highlight the query term in the snippet
     * 
     * @param string $snippet Text snippet
     * @param string $query Search term
     * @return string HTML with highlighted query
     */
    private function highlightQuery(string $snippet, string $query): string
    {
        $pattern = '/' . preg_quote($query, '/') . '/ui';
        return preg_replace($pattern, '<mark>$0</mark>', $snippet);
    }

    /**
     * Get the full page content by page number
     * 
     * @param int $pageNumber Page number to retrieve
     * @return array|null Page data or null if not found
     */
    public function getPage(int $pageNumber): ?array
    {
        foreach ($this->bookContent as $page) {
            if ($page['page'] === $pageNumber) {
                return $page;
            }
        }
        
        return null;
    }

    /**
     * Get book statistics
     * 
     * @return array Statistics about the book
     */
    public function getStats(): array
    {
        return [
            'total_pages' => count($this->bookContent),
            'first_page' => $this->bookContent[0]['page'] ?? null,
            'last_page' => $this->bookContent[count($this->bookContent) - 1]['page'] ?? null,
        ];
    }

    /**
     * Clear the cache for book content
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
