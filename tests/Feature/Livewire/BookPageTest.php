<?php

use App\Book;
use App\BookPage as AppBookPage;
use App\Livewire\BookPage;
use Livewire\Livewire;

it('should render properly', function () {

    $book = Book::factory()->create();
    $book_page = AppBookPage::factory()->create(['book_id' => $book->id]);

    Livewire::withQueryParams(['location' => $book_page->page])
        ->test(BookPage::class, ['book' => $book])
        ->assertStatus(200)
        ->assertSee($book->title);
});
