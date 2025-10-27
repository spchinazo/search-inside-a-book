<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class BookPage extends Model
{
    use HasFactory, Searchable;

    protected $fillable = ['book_id', 'page_number', 'text_content'];

    // Relacionamento com o livro
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Defines the attributes that can be used to filter searches in Meilisearch.
     */
    public static function getFilterableAttributes(): array
    {
        return ['book_id'];
    }

    /**
     * Optional: Defines which attributes are stored and returned.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'book_id' => $this->book_id,
            'page_number' => $this->page_number,
            'text_content' => $this->text_content,
        ];
    }
}