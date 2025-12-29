<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\BookSearchService;

class BookSearch extends Component
{
    public string $query = '';
    public array $results = [];
    public int $maxResults = 25;

    protected ?BookSearchService $service = null;

    public function mount(): void
    {
        $this->service = app(BookSearchService::class);
        $this->results = [];
    }

    public function updatedQuery(): void
    {
        $this->search();
    }

    public function search(): void
    {
        $this->service ??= app(BookSearchService::class);
        $this->results = $this->service->search($this->query, $this->maxResults);
    }

    public function render()
    {
        return view('livewire.book-search');
    }
}
