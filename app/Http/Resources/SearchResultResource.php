<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $formatted = $this->resource['_formatted'] ?? [];
        $snippet = $formatted['text_content'] ?? $this->resource['text_content'];
        
        if (strlen($snippet) > 400) {
            $snippet = substr($snippet, 0, 400) . '...';
        }

        return [
            'id' => $this->resource['id'],
            'page_number' => $this->resource['page_number'],
            'snippet' => $snippet,
            'full_page_url' => route('books.pages.show', [
                'book' => $this->resource['book_id'],
                'pageNumber' => $this->resource['page_number']
            ]),
            'relevance_score' => round($this->resource['_rankingScore'] ?? 0, 4),
        ];
    }
}