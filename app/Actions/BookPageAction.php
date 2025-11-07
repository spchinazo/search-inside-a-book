<?php

namespace App\Actions;

use App\BookPage;

class BookPageAction
{
    public function handle(int $book_id, int $page): BookPage
    {
        return BookPage::query()
            ->where('book_id', $book_id)
            ->where("page", $page)
            ->first();
    }
}
