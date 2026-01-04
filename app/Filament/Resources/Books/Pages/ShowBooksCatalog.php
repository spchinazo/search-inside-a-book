<?php

namespace App\Filament\Resources\Books\Pages;

use App\Models\Book;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\Books\BookResource;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\WithPagination;
use Filament\Support\Enums\Width;
use Closure;

class ShowBooksCatalog extends Page
{
    use WithPagination;

    protected static string $resource = BookResource::class;
    protected static ?string $breadcrumb = null;

    protected static ?string $slug = 'books-catalog/{record}';
    protected static ?string $icon = 'heroicon-m-book-open';

    protected string $view = 'filament.books.catalog';

    public ?Book $record = null;

    public function mount($record): void
    {
        if ($record instanceof Book) {
            $this->record = $record;
        } else {
            $this->record = static::getResource()::resolveRecordRouteBinding($record);
        }
    }

    public function getTitle(): string | Htmlable
    {
        return '';
    }

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getTableQueryProperty()
    {
        return $this->record->pages()->with(['book'])->paginate(20);
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return fn (Book $record): string => static::getUrl(['record' => $record]);
    }

    public function getFlipbookPages(): array
    {
        if (!$this->record) {
            return [];
        }

        return $this->record->pages()
            ->orderBy('page_number')
            ->get()
            ->map(function ($page) {
                $url = \Illuminate\Support\Facades\Storage::disk($page->disk)->url($page->path);
                return [
                    'src' => $url,
                    'thumb' => $url,
                    'title' => 'Page ' . $page->page_number,
                ];
            })
            ->toArray();
    }
}