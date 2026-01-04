<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookPage extends Model
{
    use SoftDeletes;

    protected $table = 'book_pages';

    protected $fillable = [
        'book_id',
        'page_number',
        'content',
        'path',
        'disk',
        'status',
        'search_vector_en',
        'search_vector_es',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function scopeSearch($query, $term, $lang = 'es') {
        $vector = $lang === 'en' ? 'search_vector_en' : 'search_vector_es';
        $config = $lang === 'en' ? 'english' : 'spanish';
        $tsQuery = "plainto_tsquery('{$config}', ?)";
    
        return $query->whereRaw("{$vector} @@ {$tsQuery}", [$term])
            ->orderByRaw("ts_rank({$vector}, {$tsQuery}) DESC", [$term]);
    }
}
