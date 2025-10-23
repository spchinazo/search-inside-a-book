<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'description',
    ];

    /**
     * Get the pages for the book.
     */
    public function pages(): HasMany
    {
        return $this->hasMany(BookPage::class);
    }
}
