<?php

namespace App\Livewire;

use App\Book;
use App\Services\BookService;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class BookSearch extends Component
{
    use WithPagination;

    public $query = '';
    public $bookId = 1; // Default to Eloquent JavaScript
    public $selectedPage = null;
    public $pageContent = null;
    public $perPage = 5;

    protected $queryString = ['query'];

    public function updatedQuery()
    {
        $this->resetPage();
        $this->selectedPage = null;
        $this->pageContent = null;
    }

    public function selectPage($pageNumber)
    {
        $this->selectedPage = $pageNumber;
        $book = Book::find($this->bookId);
        $bookService = app(BookService::class);
        $page = $bookService->getPage($book, $pageNumber);
        $this->pageContent = $page ? $page->content : 'Page not found.';
    }

    public function clearSelection()
    {
        $this->selectedPage = null;
        $this->pageContent = null;
        $this->resetPage(); // Reset pagination just in case, though it should stay on page 1
    }

    public function render(BookService $bookService)
    {
        $book = Book::find($this->bookId);
        $hits = collect();
        $total = 0;

        if (!empty($this->query)) {
            $results = $bookService->search($book, $this->query, $this->getPage(), $this->perPage);
            $hits = collect($results['hits'] ?? []);
            $total = $results['total'] ?? 0;
        }

        $paginator = new LengthAwarePaginator(
            $hits,
            $total,
            $this->perPage,
            $this->getPage(),
            ['path' => route('search')]
        );

        return view('livewire.book-search', [
            'book' => $book,
            'paginator' => $paginator,
            'total' => $total,
        ]);
    }
}
