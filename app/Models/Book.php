<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Book extends Model
{
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'author',
        'description',
    ];

    /**
     * Define the name of the index in Meilisearch.
     * By default, it would be 'books'.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'books';
    }

    /**
     * Relationship: a book has many pages.
     */
    public function pages()
    {
        return $this->hasMany(BookPage::class);
    }
}