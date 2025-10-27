<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        $formatted = $this->resource['_formatted'] ?? null;
        
        // Use the formatted text (with highlight) if available, otherwise use the original
        $snippet = $formatted['text_content'] ?? $this->resource['text_content'];
        
        // Crop the snippet if it's too long (in case Meilisearch didn't crop it)
        if (strlen($snippet) > 400) {
            $snippet = substr($snippet, 0, 400) . '...';
        }

        return [
            'id' => $this->resource['id'],
            'page_number' => $this->resource['page_number'],
            'snippet' => $snippet,
            'full_page_url' => route('pages.show', ['page' => $this->resource['id']]),
            'relevance_score' => round($this->resource['_rankingScore'] ?? 0, 4),
        ];
    }
}