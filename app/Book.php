<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'total_pages',
        'file_path',
    ];

    public function pages()
    {
        return $this->hasMany(BookPage::class);
    }
}
