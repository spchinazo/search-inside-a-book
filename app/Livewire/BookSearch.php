<?php

namespace App\Livewire;

use App\Services\BookSearchService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class BookSearch extends Component
{
    #[Url(as: 'q',keep:true)]
    public string $query = '';

    #[Url(as: 'page', keep:true)]
    public ?int $selectedPage = null;

    #[Computed()]
    public function results(){
        if(strlen(trim($this->query) < 2)){
            return collect();
        }

        return app(BookSearchService::class)->search($this->query);
    }

    #[Computed()]
    public function totalResults(){
        if(strlen(trim($this->query) < 2)){
            return 0;
        }

        return app(BookSearchService::class)->countMatches($this->query);
    }

    public function selectPage(int $pageNumber):void{
        $this->selectedPage = $pageNumber;
        $this->dispatch('page-selected', pageNumber: $pageNumber);

        $this->saveToHistory();
    }

    private function saveToHistory():void{
        if(strlen(trim($this->query))>2){
            $this->dispatch('search-saved', query: $this->query, page: $this->selectedPage);
        }
    }


    public function updatedQuery()
    {
        $this->selectedPage = null;
    }


    public function render()
    {
        return view('livewire.book-search');
    }
}
