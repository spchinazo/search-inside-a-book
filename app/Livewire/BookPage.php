<?php

namespace App\Livewire;

use Livewire\Component;
use App\Actions\BookPageAction;
use App\Actions\HighlightAction;
use App\Book;

class BookPage extends Component
{
    public $page_content;
    public $page;
    public $book_name;
    public $book_slug;
    public Book $book;
    public $term = '';
    protected $bookPageAction;
    protected $highlightAction;

    public function mount(Book $book, BookPageAction $bookPageAction, HighlightAction $highlightAction)
    {
        $this->book = $book;
        $this->page = request()->query('location', 2);
        $this->term = request()->query('term', '');
        $this->bookPageAction = $bookPageAction;
        $this->highlightAction = $highlightAction;
    }

    public function render()
    {
        $this->book_name = $this->book->title;
        $this->book_slug = $this->book->slug;

        $bookPage = $this->bookPageAction->handle($this->book->id, $this->page);
        $this->page_content = $this->highlightAction->handle($this->term, $bookPage->content);

        return view('livewire.book-page');
    }
}
