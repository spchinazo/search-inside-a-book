<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookPage extends Model
{
    protected $fillable = [
        'book_id',
        'page_number',
        'text_content',
    ];

    /**
     * Get the book that owns the page.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
