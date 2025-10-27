<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookPage extends Model
{
    use Searchable;

    protected $fillable = [
        'book_id',
        'page_number',
        'text_content',
    ];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'page_number' => $this->page_number,
            'text_content' => $this->text_content,
            'book_id' => $this->book_id,
        ];
    }

    /**
     * Get the book that owns this page.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
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
