<?php

use App\Actions\SearchAction;
use App\Book;
use App\BookPage;

it('should search properly', function () {
    $book = Book::factory()->create();
    BookPage::factory()->create([
        'book_id' => $book->id,
        'content' => 'Javascript content'
    ]);
    $bookPageAction = app(SearchAction::class)->handle('Javascript', $book->id);

    expect($bookPageAction[0]->content)->toContain('Javascript');
});
