<?php

namespace App\Livewire;

use App\Actions\HighlightAction;
use Livewire\Component;
use App\Actions\SearchAction;
use App\Book;

class BookSearch extends Component
{
    public $query = '';
    public $results = [];
    public $page = '';
    public $book_slug = '';
    public Book $book;

    public function updatedQuery()
    {
        $this->search();
    }

    public function search()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            return;
        }

        $pages = app(searchAction::class)->handle($this->query, $this->book->id);
        $highlightAction = app(highlightAction::class);

        $this->results = $pages->map(function ($page) use ($highlightAction) {

            $snippet = $highlightAction->handle($this->query, $page->content, true);

            return [
                'page' => $page->page,
                'snippet' => '...' . $snippet . '...',
            ];
        });
    }

    public function render()
    {
        return view('livewire.book-search');
    }
}
