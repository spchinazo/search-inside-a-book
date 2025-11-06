<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DocumentController extends Controller
{
    /**
     * Get the Eloquent JavaScript PDF file
     *
     * @return \Illuminate\Http\Response
     */
    public function getPdf()
    {
        $path = storage_path('exercise-files/Eloquent_JavaScript.pdf');

        if (!file_exists($path)) {
            abort(404, 'Document not found');
        }

        return Response::file($path, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Get document information
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDocumentInfo()
    {
        $path = storage_path('exercise-files/Eloquent_JavaScript.pdf');

        if (!file_exists($path)) {
            abort(404, 'Document not found');
        }

        $filename = pathinfo($path, PATHINFO_FILENAME);

        return response()->json([
            'filename' => $filename,
            'path' => '/api/documents/pdf',
        ]);
    }

    /**
     * Search for text in the document
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (empty($query)) {
            return response()->json([]);
        }

        $jsonPath = storage_path('exercise-files/Eloquent_JavaScript.json');

        if (!file_exists($jsonPath)) {
            abort(404, 'Document data not found');
        }

        $documentData = json_decode(file_get_contents($jsonPath), true);
        $results = [];

        foreach ($documentData as $item) {
            $textContent = $item['text_content'];
            $position = strpos($textContent, $query);

            if ($position !== false) {
                $snippet = $this->createSnippet($textContent, $query, $position);

                $results[] = [
                    'location' => [
                        'type' => 'page',
                        'value' => $item['page']
                    ],
                    'snippet' => $snippet,
                    'query_words' => [strtolower($query)]
                ];
            }
        }

        return response()->json($results);
    }

    /**
     * Create a snippet with the search term highlighted
     *
     * @param string $text
     * @param string $query
     * @param int $position
     * @return string
     */
    private function createSnippet($text, $query, $position)
    {
        $queryLength = strlen($query);
        $contextLength = 80;

        $start = max(0, $position - $contextLength);
        $end = min(strlen($text), $position + $queryLength + $contextLength);

        $snippet = substr($text, $start, $end - $start);

        $prefix = $start > 0 ? '... ' : '';
        $suffix = $end < strlen($text) ? ' ...' : '';

        $highlightedSnippet = str_replace(
            $query,
            "<p class='font-bold text-dark'>" . $query . "</p>",
            $snippet
        );

        return $prefix . $highlightedSnippet . $suffix;
    }
}
