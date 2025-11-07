<?php

namespace App\Actions;

class HighlightAction
{
    public function handle(string $term, string $content, $snippet = false): string
    {

        if ($snippet) {
            $pos = stripos($content, $term);
            $start = max($pos - 50, 0);
            $content = substr($content, $start, 100);
        }

        $pattern = '/' . preg_quote($term, '/') . '/i';
        $contentHighlighted = preg_replace($pattern, '<mark>$0</mark>', $content);
        return $contentHighlighted;
    }
}
