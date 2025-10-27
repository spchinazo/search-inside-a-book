<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Contracts\Queue\ShouldQueue;

class BookPage extends Model implements ShouldQueue
{
    use Searchable;

    public $queue = 'default'; 

    protected $fillable = [
        'book_id',
        'page_number',
        'text_content',
    ];

    public function getScoutKey()
    {
        return $this->id;
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'book_id' => (int) $this->book_id,
            'page_number' => (int) $this->page_number,
            'text_content' => $this->text_content,
        ];
    }

    /**
     * Get the book that owns this page.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
