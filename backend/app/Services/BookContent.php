<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use RuntimeException;

class BookContent
{
    /**
     * Load and cache the book pages from JSON, normalized as plain text per page.
     */
    public function allPages(): array
    {
        return Cache::rememberForever('book.pages', function () {
            $path = storage_path('exercise-files/Eloquent_JavaScript.json');
            if (! File::exists($path)) {
                throw new RuntimeException("Book JSON not found at {$path}");
            }

            $json = File::get($path);
            $data = json_decode($json, true);

            if (! is_array($data)) {
                throw new RuntimeException('Book JSON could not be decoded');
            }

            // Normalize each entry into plain text. If the JSON changes shape, we keep best-effort.
            $pages = array_map(function ($entry) {
                if (is_string($entry)) {
                    return $entry;
                }

                if (is_array($entry)) {
                    // Prefer explicit text_content, fallback to concatenated values.
                    if (isset($entry['text_content']) && is_string($entry['text_content'])) {
                        return $entry['text_content'];
                    }

                    // Last resort: flatten to a string to avoid breaking the consumer.
                    return trim((string) ($entry['text'] ?? $entry['content'] ?? json_encode($entry)));
                }

                return '';
            }, array_values($data));

            return $pages;
        });
    }

    /**
     * Get a single page by 1-based page number with metadata.
     */
    public function getPage(int $page): ?array
    {
        $pages = $this->allPages();
        $index = $page - 1;

        if (! isset($pages[$index])) {
            return null;
        }

        return [
            'page' => $page,
            'content' => $pages[$index],
        ];
    }

    /**
     * Count total pages.
     */
    public function totalPages(): int
    {
        return count($this->allPages());
    }
}
