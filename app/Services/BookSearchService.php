<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookSearchService
{
    private string $cacheKeyIndex = 'book_index_eloquent_js';

    private string $exerciseFilePath = 'exercise-files/Eloquent_JavaScript.json';

    public function search(string $term, int $maxResults = 25): array
    {
        $term = trim($term);

        if (!$this->isValidTerm($term)) {
            return [];
        }

        return $this->scanPages($term, $maxResults);
    }

    private function getBookIndex(): array
    {
        return Cache::remember($this->cacheKeyIndex, 3600, function () {
            Log::info('Cache miss for book index', ['key' => $this->cacheKeyIndex]);

            $path = storage_path($this->exerciseFilePath);

            if (!File::exists($path)) {
                Log::warning('Missing book index file', ['path' => $path]);
                return [];
            }

            return json_decode(File::get($path), true) ?? [];
        });
    }


    private function makeSnippet(string $text, string $term): string
    {
        $position = $this->findTermPosition($text, $term);

        if ($position === null) {
            return e(Str::limit($text, 120));
        }

        [$slice, $prefix, $suffix] = $this->cutTextWithContext($text, $term, $position);

        $highlighted = $this->highlightTerm($slice, $term);

        return $prefix . $highlighted . $suffix;
    }



    private function findTermPosition(string $text, string $term): ?int
    {
        $pos = stripos($text, $term);
        return $pos === false ? null : $pos;
    }

    private function cutTextWithContext(string $text, string $term, int $position, int $context = 60): array
    {
        $start = max(0, $position - $context);
        $length = Str::length($term) + ($context * 2);

        $slice = Str::substr($text, $start, $length);

        $prefix = $start > 0 ? '…' : '';
        $suffix = $start + $length < Str::length($text) ? '…' : '';

        return [$slice, $prefix, $suffix];
    }

    private function highlightTerm(string $text, string $term): string
    {
        $escaped = e($text);

        $pattern = '/' . preg_quote($term, '/') . '/i';

        return Str::of($escaped)
            ->replaceMatches($pattern, '<mark>$0</mark>')
            ->toString();
    }

    private function isValidTerm(string $term): bool
    {
        if ($term === '' || Str::length($term) < 2) {
            Log::warning('Search term is too short or empty', ['term' => $term]);
            return false;
        }

        return true;
    }

    private function scanPages(string $term, int $maxResults): array
    {
        $pages = $this->getBookIndex();
        $found = [];

        foreach ($pages as $entry) {
            if (!$this->pageHasText($entry)) {
                continue;
            }

            if ($this->pageContainsTerm($entry['text_content'], $term)) {
                $found[] = $this->buildResult($entry, $term);
            }

            if (\count($found) >= $maxResults) {
                break;
            }
        }

        return $found;
    }

    private function pageHasText(array $entry): bool
    {
        return !empty($entry['text_content']);
    }

    private function pageContainsTerm(string $text, string $term): bool
    {
        return str_contains($text, $term);
    }

    private function buildResult(array $entry, string $term): array
    {
        return [
            'page' => $entry['page'] ?? null,
            'snippet' => $this->makeSnippet($entry['text_content'], $term),
        ];
    }





}
