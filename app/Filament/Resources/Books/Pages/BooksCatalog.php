<?php

namespace App\Filament\Resources\Books\Pages;

use Filament\Resources\Pages\Page;
use App\Filament\Resources\Books\BookResource;
use Livewire\WithPagination;

class BooksCatalog extends Page
{
    use WithPagination;

    protected static string $resource = BookResource::class;

    protected static ?string $slug = 'books-catalog';
    protected static ?string $icon = 'heroicon-m-book';

    protected string $view = 'filament.books.list';

    public $search = '';

    public function getTitle(): string 
    {
        return __('app.catalog');
    }

    public function getTableQueryProperty()
    {
        return app(\App\Services\BooksService::class)->queryBooks($this->search);
    }


}
