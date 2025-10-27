<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class BookPage extends Model
{
    use Searchable;

    protected $fillable = [
        'book_id',
        'page_number',
        'text_content',
    ];

    /**
     * Define the data to be placed in the search index.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'page_number' => (int) $this->page_number,
            'text_content' => $this->text_content,
            'book_id' => $this->book_id, // importante para filtros
        ];
    }

    /**
     * Get the book that owns the page.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the searchable settings for Meilisearch.
     * Define filtros, campos pesquisáveis e campos ordenáveis.
     */
    public function searchableSettings(): array
    {
        return [
            'filterableAttributes' => ['book_id'],
            'searchableAttributes' => ['text_content'],
            'sortableAttributes' => ['page_number'],
        ];
    }

    /**
     * (Opcional) Campos adicionais que podem ser usados como filtros.
     */
    public function filterableAttributes(): array
    {
        return ['book_id'];
    }
}
