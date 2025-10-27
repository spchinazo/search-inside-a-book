<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResultResource extends JsonResource
{
    public function toArray($request)
    {
        $formattedContent = $this->resource->_formatted['text_content'] ?? $this->text_content;
        
        $snippet = $this->extractSnippet($formattedContent);

        return [
            'id' => $this->id,
            'page_number' => $this->page_number,
            'snippet' => $snippet,
            'full_page_url' => route('pages.show', $this->id),
        ];
    }

    /**
     * Tries to extract a snippet focused on the first occurrence of the highlight.
     */
    protected function extractSnippet(string $formattedContent): string
    {
        $highlightTag = '<em>';
        $radius = 70;

        $pos = strpos($formattedContent, $highlightTag);

        if ($pos === false) {
            return mb_substr(strip_tags($this->text_content), 0, 150) . '...';
        }

        $start = max(0, $pos - $radius);
        
        $prefix = ($start > 0) ? '... ' : '';
        
        $snippet = mb_substr($formattedContent, $start, (2 * $radius) + 10);
        
        return $prefix . $snippet . ' ...';
    }
}
