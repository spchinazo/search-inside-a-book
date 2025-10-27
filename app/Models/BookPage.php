<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class BookPage extends Model
{
    use Searchable;

    public function toSearchableArray()
    {
        return [
            'text_content' => $this->text_content,
            'book_id' => $this->book_id,
        ];
    }

    /**
     * Meilisearch options
     */
    public function searchableOptions(): array
    {
        return [
            'attributesToHighlight' => ['text_content'],
            'highlightPreTag' => '<em>',
            'highlightPostTag' => '</em>',
        ];
    }
}
