<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookPageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'book_id' => $this->book_id ?? $this['book_id'],
            'page_number' => $this->page_number ?? $this['page_number'],
            'snippet' => $this['snippet'] ?? null,
            'matches' => $this['matches'] ?? [],
        ];
    }
}
