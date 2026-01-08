<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'page_number',
        'text_content',
    ];

    #[Scope]
    protected function search(Builder $query, string $searchTerm): void
    {
        $query->whereRaw(
            "text_search_vector @@ plainto_tsquery('english',?)",
            [$searchTerm]
        )->orderBy('page_number');
    }

    #[Scope]
    protected function withSearchRank(Builder $query, string $searchTerm): void
    {
        $query->selectRaw(
            "*, ts_rank(text_search_vector, plainto_tsquery('english',?)) as search_rank",
            [$searchTerm]
        );

    }
}
