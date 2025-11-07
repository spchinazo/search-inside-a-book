<?php

namespace App\Actions;

use App\BookPage;
use Illuminate\Database\Eloquent\Collection;

class SearchAction
{
    public function handle(string $query, int $book_id): Collection
    {
        return BookPage::search($query)
            ->where('book_id', $book_id)
            ->take(10)
            ->get();
    }
}
