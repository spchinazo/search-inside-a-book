<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    protected $table = 'books';

    protected $fillable = [
        'title',
        'lang',
        'isbn',
        'path',
        'disk',
        'front',
    ];

    public function pages()
    {
        return $this->hasMany(BookPage::class)
            ->select([
                'page_number',
                'path',
                'disk',
            ]);
    }
}
