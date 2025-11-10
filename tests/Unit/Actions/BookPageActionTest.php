<?php

use App\Actions\BookPageAction;
use App\Book;
use App\BookPage;

it('returns the proper book page model', function () {
    $book = Book::factory()->create();
    $bookPage = BookPage::factory()->create([
        'book_id' => $book->id
    ]);
    $bookPageAction = app(BookPageAction::class)->handle($bookPage->id, $bookPage->page);

    expect($bookPage->id)->toEqual($bookPageAction->id);
});
