<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class BookPage extends Model
{
    protected $fillable = [
        'book_id',
        'page_number',
        'content',
    ];

    use Searchable;

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'book_id' => (int) $this->book_id,
            'page_number' => (int) $this->page_number,
            'content' => $this->content,
        ];
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
