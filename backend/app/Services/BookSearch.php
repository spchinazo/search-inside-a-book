<?php

namespace App\Services;

use Illuminate\Support\Str;

class BookSearch
{
    private BookContent $content;

    public function __construct(BookContent $content)
    {
        $this->content = $content;
    }

    /**
     * Search term across all pages, case-insensitive.
     * Supports pagination by offset/limit and returns total.
     */
    public function search(
        string $term,
        int $limit = 20,
        int $maxMatchesPerPage = 1,
        int $offset = 0
    ): array {
        $term = trim($term);
        if ($term === '') {
            return ['data' => [], 'total' => 0];
        }

        $needle = Str::lower($term);
        $pages = $this->content->allPages();

        $allMatches = [];

        foreach ($pages as $index => $text) {
            if (! is_string($text)) {
                continue; // skip malformed entries
            }

            $haystack = Str::lower($text);
            $pos = 0;
            $matchesOnPage = 0;

            while (($pos = mb_stripos($haystack, $needle, $pos)) !== false) {
                $allMatches[] = [
                    'page' => $index + 1,
                    'page_id' => $index + 1,
                    'snippet' => $this->buildSnippet($text, $pos, mb_strlen($term)),
                ];

                $matchesOnPage++;
                if ($matchesOnPage >= $maxMatchesPerPage) {
                    break; // keep it simple: limit matches per page
                }

                $pos += mb_strlen($term);
            }
        }

        $total = count($allMatches);

        if ($offset < 0) {
            $offset = 0;
        }

        $paged = $limit > 0 ? array_slice($allMatches, $offset, $limit) : $allMatches;

        return [
            'data' => $paged,
            'total' => $total,
        ];
    }

    /**
     * Build a short snippet around the match position.
     */
    private function buildSnippet(string $text, int $matchPos, int $matchLen, int $window = 80): string
    {
        $start = max(0, $matchPos - $window);
        $end = min(mb_strlen($text), $matchPos + $matchLen + $window);

        $prefix = $start > 0 ? '...' : '';
        $suffix = $end < mb_strlen($text) ? '...' : '';

        $slice = mb_substr($text, $start, $end - $start);

        // Collapse excessive whitespace/newlines for cleaner snippet.
        $slice = preg_replace('/\s+/u', ' ', $slice) ?? $slice;

        return $prefix . $slice . $suffix;
    }
}
