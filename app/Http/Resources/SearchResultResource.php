<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Pega o snippet destacado do MeiliSearch (_formatted) ou cai no texto completo
        $formattedContent = $this->resource->_formatted['text_content'] ?? $this->resource->text_content ?? '';

        return [
            'id' => $this->resource->id,
            'page_number' => $this->resource->page_number,
            'snippet' => $this->extractSnippet($formattedContent),
            'full_page_url' => route('pages.show', $this->resource->id),
        ];
    }

    /**
     * Extrai um snippet focado na primeira ocorrência do highlight.
     */
    protected function extractSnippet(string $formattedContent): string
    {
        $highlightTag = '<em>';
        $radius = 70;

        $pos = strpos($formattedContent, $highlightTag);

        if ($pos === false) {
            return mb_substr(strip_tags($formattedContent), 0, 150) . '...';
        }

        $start = max(0, $pos - $radius);
        $prefix = ($start > 0) ? '... ' : '';
        $snippet = mb_substr($formattedContent, $start, (2 * $radius) + 10);

        return $prefix . $snippet . ' ...';
    }
}
