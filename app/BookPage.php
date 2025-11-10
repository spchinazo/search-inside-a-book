<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Laravel\Scout\Attributes\SearchUsingFullText;

class BookPage extends Model
{
    /** @use HasFactory<\Database\Factories\BookPageFactory> */
    use HasFactory, Searchable;

    protected $fillable = ['book_id', 'page', 'content'];

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    #[SearchUsingFullText(['content'])]
    public function toSearchableArray(): array
    {
        return [
            'content' => $this->content
        ];
    }
}
