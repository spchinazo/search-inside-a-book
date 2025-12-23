<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchController extends Controller
{
    private function loadBookData()
    {
        $jsonPath = storage_path('exercise-files/Eloquent_JavaScript.json');
        if (!file_exists($jsonPath)) {
            return [];
        }
        return json_decode(file_get_contents($jsonPath), true);
    }

    public function index(Request $request)
    {
        $query = $request->get('q');
        $results = null;

        if ($query) {
            $bookData = $this->loadBookData();
            $results = $this->searchInBook($bookData, $query);
        }

        return view('search', compact('query', 'results'));
    }

    public function show($page)
    {
        $bookData = $this->loadBookData();
        $pageData = collect($bookData)->firstWhere('page', (int)$page);

        if (!$pageData) {
            abort(404);
        }

        return view('page', compact('pageData'));
    }

    private function searchInBook($bookData, $query)
    {
        $results = [];
        $query = strtolower($query);

        foreach ($bookData as $page) {
            $text = strtolower($page['text_content']);
            if (strpos($text, $query) !== false) {
                // Find snippet around the query
                $snippet = $this->getSnippet($page['text_content'], $query);
                $results[] = [
                    'page' => $page['page'],
                    'snippet' => $snippet,
                ];
            }
        }

        return $results;
    }

    private function getSnippet($text, $query, $length = 200)
    {
        $pos = stripos($text, $query);
        if ($pos === false) {
            return substr($text, 0, $length) . '...';
        }

        $start = max(0, $pos - $length / 2);
        $end = min(strlen($text), $pos + strlen($query) + $length / 2);

        $snippet = substr($text, $start, $end - $start);
        if ($start > 0) {
            $snippet = '...' . $snippet;
        }
        if ($end < strlen($text)) {
            $snippet .= '...';
        }
        // Resaltar de forma segura el término buscado (evita inyección)
        if (strlen($query) > 0) {
            $escapedSnippet = e($snippet);
            $escapedQuery = preg_quote($query, '/');
            $highlighted = preg_replace(
                "/($escapedQuery)/i",
                '<mark>$1</mark>',
                $escapedSnippet
            );
            return $highlighted;
        }
        return e($snippet);
    }
}
